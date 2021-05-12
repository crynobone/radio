window.Radio = {
    token: document.currentScript.dataset.token,
}

Radio.mount = function (component, state = {}, methods = [], route = '/radio/call') {
    return {
        ...state,
        ...methods.reduce(function (methods, method) {
            methods[method] = Radio.call(component, method, route)

            return methods
        }, {}),
        $radio: {
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

Radio.call = function (component, method, route) {
    return async function (...args) {
        this.$radio.errors.reset()

        this.$radio.processing = true

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
                'X-CSRF-Token': window.Radio.token,
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then(async res => {
            this.$radio.processing = false

            const response = await res.text()

            let json

            try {
                json = JSON.parse(response)
            } catch (error) {
                this.showHtmlModal(response)

                return
            }

            if (! res.ok && json.errors) {
                this.$radio.errors.store = json.errors

                return res
            }

            Object.entries(json.state).forEach(entry => {
                const [key, value] = entry

                if (this[key] !== value) {
                    this[key] = value
                }
            })

            return json.result
        }).catch(error => {
            console.log(error)
        })
    }
}

Radio.showHtmlModal = function (html) {
    let page = document.createElement('html')
    page.innerHTML = html
    page.querySelectorAll('a').forEach(a =>
        a.setAttribute('target', '_top')
    )

    let modal = document.getElementById('radio-error')

    if (typeof modal != 'undefined' && modal != null) {
        modal.innerHTML = ''
    } else {
        modal = document.createElement('div')
        modal.id = 'radio-error'
        modal.style.position = 'fixed'
        modal.style.width = '100vw'
        modal.style.height = '100vh'
        modal.style.padding = '50px'
        modal.style.top = 0
        modal.style.left = 0
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

Radio.hideHtmlModal = function (modal) {
    modal.outerHTML = ''
    document.body.style.overflow = 'visible'
}