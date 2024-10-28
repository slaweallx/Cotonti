let CotontiPeriodicalRequest = null;

(function () {
    /**
     * Callback for CotontiPeriodicalRequest.add()
     *
     * @callback addPeriodicalRequestCallback
     * @param {Object} result Result object
     */

    /**
     * Periodical request class
     */
    CotontiPeriodicalRequest = class  {

        /**
         * @type {Map<string, RequestItem>}
         */
        #requestsRegistry;

        #executionTimeOutId = null;

        #executing = false;

        constructor() {
            this.#requestsRegistry = new Map();
        }

        /**
         * Add periodical request to registry.
         * @param {string} id Unique code. Will be passed to server with other parameters
         * @param {Object} params
         * @param {addPeriodicalRequestCallback} callback
         * @param {number} timeout In seconds
         */
        add(id, params, callback, timeout) {
            this.#requestsRegistry.set(id, new RequestItem(params, callback, timeout));
            this.#setExecution();
        }

        /**
         * Remove periodical request from registry.
         * @param {string} id Unique code
         */
        remove(id) {
            this.#requestsRegistry.delete(id);
            this.#setExecution();
        }

        #setExecution() {
            let minTimeout = 0;
            this.#requestsRegistry.forEach((item, key, map) => {
                if (minTimeout === 0 || minTimeout > item.timeout) {
                    minTimeout = item.timeout;
                }
            });

            if (this.#executionTimeOutId !== null) {
                window.clearTimeout(this.#executionTimeOutId);
                this.#executionTimeOutId = null;
            }

            this.#executionTimeOutId = window.setTimeout(() => {this.#execute()}, minTimeout * 1000);
        }

        #setExecuting() {

        }

        #execute() {
            if (this.#executing) {
                return;
            }

            this.#executing = true;

            console.log('Execute request start');

            const date = Date.now();
            let currentDate = null;
            do {
                currentDate = Date.now();
            } while (currentDate - date < 2000);

            console.log('Execute request end');

            //this.#executing = false;
        }
    }

    class RequestItem {
        /**
         * @type {Object}
         * @public
         */
        params;

        /**
         * @type {addPeriodicalRequestCallback}
         */
        callback;

        /**
         * @type {number}
         */
        timeout;

        /**
         * @param {Object} params
         * @param {addPeriodicalRequestCallback} callback
         * @param {number} timeout in seconds
         */
        constructor(params, callback, timeout) {
            this.params = params;
            this.callback = callback;
            this.timeout = timeout;
        }
    }
})();
