function initCommentToggles() {
    document.querySelectorAll('.comment-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const imageId = button.dataset.imageId;
            const commentForm = document.getElementById(`comments-${imageId}`);
            if (commentForm) {
                commentForm.classList.toggle('d-none');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let currentPage = window.galleryConfig?.initialPage || 1;
    let loading = false;

    async function loadMoreImages() {
        if (loading) return;
        loading = true;
        document.getElementById('loading').classList.remove('d-none');
        currentPage++;
        try {
            const response = await fetch(`?ajax=1&page=${currentPage}`);
            if (!response.ok) throw new Error("Network response was not ok");
            const html = await response.text();
            if (html.trim() === "") {
                window.removeEventListener('scroll', handleScroll);
            } else {
                document.getElementById('gallery-grid').insertAdjacentHTML('beforeend', html);
                initCommentToggles();
            }
        } catch (error) {
            // console.error("Error loading more images:", error);
            alert("Error loading more images:" + error);
        } finally {
            document.getElementById('loading').classList.add('d-none');
            loading = false;
        }
    }

    function handleScroll() {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
            loadMoreImages();
        }
    }

    initCommentToggles();
    
    window.addEventListener('scroll', handleScroll);
});