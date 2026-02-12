export default async function () {
    const res = await fetch("/wp-json/wp/v2/posts");
    const posts = await res.json();
    return {
        tag: "div",
        children: posts.map(p => ({
            tag: "div",
            children: [
                { tag: "h2", html: p.title.rendered },
                {
                    tag: "a",
                    text: "Read More",
                    attrs: { href: `/post/${p.id}` }
                }
            ]
        }))
    };
}
