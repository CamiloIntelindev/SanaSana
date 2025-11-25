/**
 * Sanasana General Scripts - Optimized
 * @package Sanasana
 * @version 1.1.0
 * 
 * Optimizations:
 * - Singleton pattern for NotifyController
 * - Error boundary
 * - Reduced global scope pollution
 */

(function($) {
	'use strict';

	/**
	 * Notification Controller (Singleton)
	 * Manages Notyf toast notifications across the plugin
	 */
	class NotifyController {
		constructor() {
			if (NotifyController.instance) {
				return NotifyController.instance;
			}

			// Check if Notyf is available
			if (typeof Notyf === 'undefined') {
				console.warn('Sanasana: Notyf library not loaded. Notifications disabled.');
				this.notify = null;
			} else {
				this.notify = new Notyf({
					position: { x: 'right', y: 'top' },
					duration: 3000,
					dismissible: true
				});
			}

			NotifyController.instance = this;
		}

		/**
		 * Show notification message
		 * @param {string} message - Message to display
		 * @param {boolean} error - Is error (red) or success (green)
		 */
		showMessage(message, error = true) {
			if (!this.notify) {
				// Fallback to console if Notyf not available
				error ? console.error(message) : console.log(message);
				return;
			}
			error ? this.notify.error(message) : this.notify.success(message);
		}

		/**
		 * Show success message (shorthand)
		 * @param {string} message
		 */
		success(message) {
			this.showMessage(message, false);
		}

		/**
		 * Show error message (shorthand)
		 * @param {string} message
		 */
		error(message) {
			this.showMessage(message, true);
		}
	}

	// Expose globally for backward compatibility with existing code
	window.notifyController = new NotifyController();

})(jQuery);
