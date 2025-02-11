document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.comment-toggle').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const imageId = button.getAttribute('data-image-id');
            const commentForm = document.getElementById(`comments-${imageId}`);
            console.log("clicked: " + imageId + " -commentForm:" + commentForm.classList);
            
            if (commentForm) {
                if (commentForm.classList.contains('d-none')) {
                    commentForm.classList.remove('d-none');
                    commentForm.classList.add('d-block');
                    console.log("1");
                    
                } else {
                    commentForm.classList.add('d-none');
                    commentForm.classList.remove('d-block');
                    console.log("2");
                }
                console.log("Finish -commentForm:" + commentForm.classList);
            }
        });
    });
});