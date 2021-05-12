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
            const json = await res.json()
            const html = await res.text()

            if (! res.ok && json.errors) {
                this.$aerial.errors.store = json.errors
                return res
            }

            if (!! html.text().match(/<script>Sfdump\(".+"\)<\/script>/)) {
                this.showHtmlModal(html)
            }

            Object.entries(json.state).forEach(entry => {
                const [key, value] = entry

                if (this[key] !== value) {
                    this[key] = value
                }
            })

            this.$aerial.processing = false

            return json.result
        }).catch(error => {
            console.log(error)
        });
    }
}

Aerial.showHtmlModal = function (html) {
    let page = document.createElement('html')
    page.innerHTML = html
    page.querySelectorAll('a').forEach(a =>
        a.setAttribute('target', '_top')
    )

    let modal = document.getElementById('aerial-error')

    if (typeof modal != 'undefined' && modal != null) {
        modal.innerHTML = ''
    } else {
        modal = document.createElement('div')
        modal.id = 'aerial-error'
        modal.style.position = 'fixed'
        modal.style.width = '100vw'
        modal.style.height = '100vh'
        modal.style.padding = '50px'
        modal.style.backgroundColor = 'rgba(0, 0, 0, .6)'
        modal.style.zIndex = 200000
    }

    let iframe = document.createElement('iframe')
    iframe.style.backgroundColor = '#17161A'
    iframe.style.borderRadius = '5px'
    iframe.style.width = '100%'
    iframe.style.height = '100%'
    modal.appendChild(iframe)

    document.body.prepend(modal)
    document.body.style.overflow = 'hidden'
    iframe.contentWindow.document.open()
    iframe.contentWindow.document.write(page.outerHTML)
    iframe.contentWindow.document.close()

    modal.addEventListener('click', () => this.hideHtmlModal(modal))
    modal.setAttribute('tabindex', 0)
    modal.addEventListener('keydown', e => {
        if (e.key === 'Escape') this.hideHtmlModal(modal)
    })
    modal.focus()
}

Aerial.hideHtmlModal = function (modal) {
    modal.outerHTML = ''
    document.body.style.overflow = 'visible'
}