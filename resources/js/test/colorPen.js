// resources/js/colorPen.js

(function () {
    // Only run after DOM ready
    const ready = (fn) => {
        if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", fn);
        else fn();
    };

    ready(() => {
        const questionDiv = document.getElementById("questionDiv");
        if (!questionDiv) return;

        // storage key (exercise_id bilan)
        const exEl = document.getElementById("exercise_id");
        const exerciseId = exEl ? exEl.value : "";
        const pageType = window.location.pathname.includes("listening") ? "listening" : "reading";
        window.STORAGE_PEN_COLOR_KEY = "cefr-" + pageType + "-pen-color-" + exerciseId;

        // UI elements
        const box = document.getElementById("penColorBox");
        const dropdown = document.getElementById("penColorDropdown");
        const preview = document.getElementById("penColorPreview");
        const swatches = document.querySelectorAll("#penColorDropdown .pen-swatch");

        if (!box || !dropdown || !preview || !swatches.length) return;

        // ---- state ----
        let selectedColor = localStorage.getItem(window.STORAGE_PEN_COLOR_KEY) || ""; // "" = default(remove)

        function setPreview(color) {
            if (!color) {
                preview.style.backgroundColor = "#ffffff";
                preview.style.borderColor = "#9ca3af"; // gray-400
            } else {
                preview.style.backgroundColor = color;
                preview.style.borderColor = "#9ca3af";
            }
        }

        function saveColor(color) {
            selectedColor = color || "";
            localStorage.setItem(window.STORAGE_PEN_COLOR_KEY, selectedColor);
            setPreview(selectedColor);
        }

        // init
        setPreview(selectedColor);

        // ---- dropdown open/close ----
        function openDropdown() {
            dropdown.classList.remove("hidden");
        }
        function closeDropdown() {
            dropdown.classList.add("hidden");
        }
        function toggleDropdown() {
            if (dropdown.classList.contains("hidden")) openDropdown();
            else closeDropdown();
        }

        box.addEventListener("click", (e) => {
            // swatch bosilganda ham box click tushadi, shuni to‘xtatamiz
            if (e.target && e.target.classList && e.target.classList.contains("pen-swatch")) return;
            toggleDropdown();
        });

        document.addEventListener("click", (e) => {
            if (!box.contains(e.target)) closeDropdown();
        });

        // pick color
        swatches.forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                const c = btn.getAttribute("data-color") || "";
                saveColor(c);
                closeDropdown();
            });
        });

        // ---- helpers for highlighting ----

        function selectionInsideQuestionDiv(sel) {
            if (!sel || sel.rangeCount === 0) return false;
            const range = sel.getRangeAt(0);
            const common = range.commonAncestorContainer;
            const node = common.nodeType === 1 ? common : common.parentNode;
            return node && questionDiv.contains(node);
        }

        // unwrap a node (remove span but keep children)
        function unwrap(el) {
            const parent = el.parentNode;
            if (!parent) return;
            while (el.firstChild) parent.insertBefore(el.firstChild, el);
            parent.removeChild(el);
        }

        // remove highlights that intersect with range
        function removeHighlightsInRange(range) {
            const spans = questionDiv.querySelectorAll('span[data-hl="1"]');
            spans.forEach((sp) => {
                try {
                    if (range.intersectsNode(sp)) {
                        unwrap(sp);
                    }
                } catch (err) {
                    // intersectsNode ba'zi brauzerlarda xatolik berishi mumkin
                    // fallback: compare boundaries
                    try {
                        const r2 = document.createRange();
                        r2.selectNodeContents(sp);
                        const endsBefore =
                            range.compareBoundaryPoints(Range.END_TO_START, r2) <= 0;
                        const startsAfter =
                            range.compareBoundaryPoints(Range.START_TO_END, r2) >= 0;
                        if (!endsBefore && !startsAfter) unwrap(sp);
                    } catch (_) {}
                }
            });
        }

        // apply highlight (wrap extracted contents)
        function applyHighlight(range, color) {
            // if selection already includes highlight spans, first remove them (to avoid nesting)
            removeHighlightsInRange(range);

            const span = document.createElement("span");
            span.setAttribute("data-hl", "1");
            span.style.backgroundColor = color;
            span.style.padding = "0 2px";

            const frag = range.extractContents();
            span.appendChild(frag);
            range.insertNode(span);

            // collapse selection after insert
            const sel = window.getSelection();
            sel.removeAllRanges();
        }

        // ---- main: mouse selection handler ----
        function onMouseUp() {
            const sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return;
            if (sel.isCollapsed) return;

            // only inside questionDiv
            if (!selectionInsideQuestionDiv(sel)) return;

            const range = sel.getRangeAt(0);

            // avoid inputs/selects/textarea content
            const common = range.commonAncestorContainer.nodeType === 1
                ? range.commonAncestorContainer
                : range.commonAncestorContainer.parentNode;

            if (common) {
                const bad = common.closest && common.closest("input, textarea, select, button");
                if (bad) return;
            }

            // default color => remove highlight in selected area
            if (!selectedColor) {
                removeHighlightsInRange(range);
                sel.removeAllRanges();
                return;
            }

            // apply highlight
            applyHighlight(range, selectedColor);
        }

        // listen on questionDiv only
        questionDiv.addEventListener("mouseup", onMouseUp);

        // Optional: touch support (mobile)
        questionDiv.addEventListener("touchend", () => {
            // small delay: selection completes after touchend
            setTimeout(onMouseUp, 0);
        });

        // expose helpers (optional)
        window.getSelectedPenColor = () => selectedColor;
        window.clearAllHighlights = () => {
            questionDiv.querySelectorAll('span[data-hl="1"]').forEach(unwrap);
        };
    });
})();
