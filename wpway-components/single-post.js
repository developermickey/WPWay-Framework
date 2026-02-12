export default async function (params) {
    const res = await fetch(`/wp-json/wp/v2/posts/${params.id}`);
    const post = await res.json();
    return {
        tag: "div",
        children: [
            { tag: "h1", html: post.title.rendered },
            { tag: "div", html: post.content.rendered }
        ]
    };
}
