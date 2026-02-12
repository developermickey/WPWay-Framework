class WPWay {

    constructor() {
        this.contentArea = document.querySelector("#primary");
        this.oldVDOM = null;

        this.createLoader();
        this.bindLinks();

        window.onpopstate = () => this.loadPage(location.href, false);
    }

    createLoader() {
        this.loader = document.createElement("div");
        this.loader.className = "wpway-loader";
        document.body.appendChild(this.loader);
    }

    showLoader() {
        this.loader.classList.add("active");
    }

    hideLoader() {
        setTimeout(() => {
            this.loader.classList.remove("active");
        }, 300);
    }

    bindLinks() {
        document.addEventListener("click", (e) => {

            const link = e.target.closest("a");
            if (!link) return;

            const url = link.href;

            if (url.includes(location.origin)) {
                e.preventDefault();
                this.loadPage(url, true);
            }
        });
    }

    async loadPage(url, push = true) {

        this.showLoader();

        const response = await fetch(`${WPWAY.rest_url}?url=${url}`);
        const data = await response.json();

        if (data.error) return;

        if (push) history.pushState({}, "", url);

        const newVDOM = this.createVDOM(data);

        if (!this.oldVDOM) {
            this.renderInitial(newVDOM);
        } else {
            this.patch(this.contentArea, newVDOM, this.oldVDOM);
        }

        this.oldVDOM = newVDOM;

        document.title = data.title;

        this.hideLoader();
    }

    createVDOM(data) {
        return {
            tag: "div",
            children: [
                { tag: "h1", text: data.title },
                { tag: "div", html: data.content }
            ]
        };
    }

    renderInitial(vdom) {
        const node = this.render(vdom);
        this.contentArea.innerHTML = "";
        this.contentArea.appendChild(node);
    }

    render(vnode) {

        const el = document.createElement(vnode.tag);

        if (vnode.text) {
            el.textContent = vnode.text;
        }

        if (vnode.html) {
            el.innerHTML = vnode.html;
        }

        if (vnode.children) {
            vnode.children.forEach(child => {
                el.appendChild(this.render(child));
            });
        }

        return el;
    }

    patch(parent, newNode, oldNode, index = 0) {

        if (!oldNode) {
            parent.appendChild(this.render(newNode));
            return;
        }

        if (!newNode) {
            parent.removeChild(parent.childNodes[index]);
            return;
        }

        if (this.changed(newNode, oldNode)) {
            parent.replaceChild(
                this.render(newNode),
                parent.childNodes[index]
            );
            return;
        }

        if (newNode.tag) {

            const newLength = newNode.children?.length || 0;
            const oldLength = oldNode.children?.length || 0;

            for (let i = 0; i < newLength || i < oldLength; i++) {
                this.patch(
                    parent.childNodes[index],
                    newNode.children?.[i],
                    oldNode.children?.[i],
                    i
                );
            }
        }
    }

    changed(node1, node2) {

        return typeof node1 !== typeof node2 ||
            ((node1.text || node1.html) !== (node2.text || node2.html)) ||
            node1.tag !== node2.tag;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new WPWay();
});

