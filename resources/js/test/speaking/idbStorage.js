/* idbStorage.js - speaking
   - IndexedDB blob store for recordings
   - save/load/delete by STORAGE_KEY + seq
*/

(function () {
    const DB_NAME = 'cefr-speaking';
    const DB_VERSION = 1;
    const STORE_NAME = 'records';

    function openDb() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);

            req.onupgradeneeded = () => {
                const db = req.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    const store = db.createObjectStore(STORE_NAME, { keyPath: 'key' });
                    store.createIndex('storageKey', 'storageKey', { unique: false });
                }
            };

            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    // key: `${storageKey}-seq-${seq}`
    function makeKey(storageKey, seq) {
        return `${storageKey}-seq-${Number(seq || 0)}`;
    }

    window.idbSaveRecording = async function (storageKey, seq, blob) {
        if (!storageKey || !seq || !blob) return false;

        const db = await openDb();
        const key = makeKey(storageKey, seq);

        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);

            store.put({
                key,
                storageKey,
                seq: Number(seq),
                mime: blob.type || 'audio/webm',
                size: blob.size || 0,
                saved_at: new Date().toISOString(),
                blob
            });

            tx.oncomplete = () => resolve(true);
            tx.onerror = () => reject(tx.error);
        });
    };

    window.idbLoadRecording = async function (storageKey, seq) {
        if (!storageKey || !seq) return null;

        const db = await openDb();
        const key = makeKey(storageKey, seq);

        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const req = store.get(key);

            req.onsuccess = () => resolve(req.result?.blob || null);
            req.onerror = () => reject(req.error);
        });
    };

    window.idbDeleteRecording = async function (storageKey, seq) {
        if (!storageKey || !seq) return false;

        const db = await openDb();
        const key = makeKey(storageKey, seq);

        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).delete(key);

            tx.oncomplete = () => resolve(true);
            tx.onerror = () => reject(tx.error);
        });
    };

    // delete all blobs for one STORAGE_KEY (new attempt / reload)
    window.idbClearRecordingsByStorageKey = async function (storageKey) {
        if (!storageKey) return false;

        const db = await openDb();

        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            const store = tx.objectStore(STORE_NAME);
            const idx = store.index('storageKey');

            const req = idx.openCursor(IDBKeyRange.only(storageKey));

            req.onsuccess = () => {
                const cursor = req.result;
                if (cursor) {
                    cursor.delete();
                    cursor.continue();
                }
            };

            tx.oncomplete = () => resolve(true);
            tx.onerror = () => reject(tx.error);
        });
    };

    // optional: check how many saved
    window.idbCountRecordingsByStorageKey = async function (storageKey) {
        if (!storageKey) return 0;

        const db = await openDb();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const store = tx.objectStore(STORE_NAME);
            const idx = store.index('storageKey');

            let count = 0;
            const req = idx.openCursor(IDBKeyRange.only(storageKey));
            req.onsuccess = () => {
                const cursor = req.result;
                if (!cursor) return;
                count += 1;
                cursor.continue();
            };
            tx.oncomplete = () => resolve(count);
            tx.onerror = () => reject(tx.error);
        });
    };
})();
