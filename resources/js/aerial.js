window.Aerial = {
    token: document.currentScript.dataset.token,
}

Aerial.mount = function (component, state = {}, methods = [], route = '/aerial/call') {
    return {
        ...state,
        ...methods.reduce(function (methods, method) {
            methods[method] = Aerial.call(component, method, route)

            return methods
        }, {}),
        $aerial: {
            processing: false,
            errors: {
                store: {},
                any() {
                    return Object.values(this.store).length > 0
                },
                all() {
                    return this.store
                },
                get(key) {
                    return this.store[key]
                },
                has(key) {
                    return this.store[key] !== undefined
                },
                reset() {
                    this.store = {}
                }
            }
        }
    }
}

Aerial.call = function (component, method, route) {
    return async function (...args) {
        this.$aerial.errors.reset()

        this.$aerial.processing = true

        const state = Object.fromEntries(Object.entries(this).filter(entry => {
            const [name, value] = entry

            return ! name.startsWith('$') && typeof value !== 'function'
        }))

        const body = {
            component,
            state,
            method,
            args,
        }

        return fetch(route, {
            method: 'POST',
            body: JSON.stringify(body),
            credentials: 'same-origin',
            headers: {
                'Accepts': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.Aerial.token,
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then(async res => {
            const response = await res.json()

            if (! res.ok && response.errors) {
                this.$aerial.errors.store = response.errors
                return res
            }

            Object.entries(response.state).forEach(entry => {
                const [key, value] = entry

                this[key] = value
            })

            this.$aerial.processing = false

            return response.result
        }).catch(error => {
            console.log(error)
        });
    }
}
