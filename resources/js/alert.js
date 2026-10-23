document.addEventListener('DOMContentLoaded', function() {

    // Global alert function
    window.showAlert =function(message, type = 'success', duration = 5000) {
        const alertContainer = document.getElementById('alert-container');
        const alertId = 'alert-' + Date.now();

        // Alert types configuration
        const alertTypes = {
            success: {
                bgColor: 'bg-green-50',
                borderColor: 'border-green-200',
                textColor: 'text-green-800',
                iconColor: 'text-green-600',
                icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>`
            },
            error: {
                bgColor: 'bg-red-50',
                borderColor: 'border-red-200',
                textColor: 'text-red-800',
                iconColor: 'text-red-600',
                icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>`
            },
            warning: {
                bgColor: 'bg-yellow-50',
                borderColor: 'border-yellow-200',
                textColor: 'text-yellow-800',
                iconColor: 'text-yellow-600',
                icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>`
            },
            info: {
                bgColor: 'bg-blue-50',
                borderColor: 'border-blue-200',
                textColor: 'text-blue-800',
                iconColor: 'text-blue-600',
                icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>`
            }
        };

        const config = alertTypes[type] || alertTypes.success;

        // Create alert element
        const alertElement = document.createElement('div');
        alertElement.id = alertId;
        alertElement.className = `${config.bgColor} ${config.borderColor} ${config.textColor} border rounded-lg p-4 shadow-lg transform translate-x-full transition-all duration-300 ease-in-out`;

        alertElement.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0 ${config.iconColor}">
                    ${config.icon}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="removeAlert('${alertId}')" class="inline-flex ${config.textColor} hover:${config.textColor} focus:outline-none focus:${config.textColor}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        // Add to container
        alertContainer.appendChild(alertElement);

        // Animate in
        setTimeout(() => {
            alertElement.classList.remove('translate-x-full');
            alertElement.classList.add('translate-x-0');
        }, 100);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                removeAlert(alertId);
            }, duration);
        }
    };

    // Remove alert function
    window.removeAlert = function (alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (alertElement.parentNode) {
                    alertElement.parentNode.removeChild(alertElement);
                }
            }, 300);
        }
    };

    window.highlightElements = function () {
        const elements = document.querySelectorAll('.highlight-element');

        elements.forEach((el) => {
            el.classList.add('attention-glow');

            // 5 sekunddan keyin effect olib tashlanadi
            setTimeout(() => {
                el.classList.remove('attention-glow');
            }, 5000);
        });
    };
    window.highlightCreditElement = function () {
        const elements = document.querySelectorAll('.highlight-credit-element');

        elements.forEach((el) => {
            el.classList.add('attention-glow');

            // 5 sekunddan keyin effect olib tashlanadi
            setTimeout(() => {
                el.classList.remove('attention-glow');
            }, 5000);
        });
    };

    document.addEventListener('global_alert', function(data) {
        // Main global_alert event listener

        console.log('aaaa')
        let type=data['detail']['0']
        let message=data['detail']['1']
        let alertType;

        switch(type) {
            case 0:
                alertType = 'error';
                break;
            case 1:
                alertType = 'success';
                break;
            case 2:
                alertType = 'warning';
                break;
            case 3:
                alertType = 'info';
                break;
            default:
                alertType = 'success';
        }

        showAlert(message, alertType, 5000);

    });
});
