document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const username = loginForm.querySelector('input[name="username"]').value;
            const password = loginForm.querySelector('input[name="password"]').value;

            if (username.trim() === '' || password.trim() === '') {
                alert('Username and password cannot be empty.');
                event.preventDefault(); // Prevent form submission
            }
        });
    }

    const registerForm = document.querySelector('form[action="register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const username = registerForm.querySelector('input[name="username"]').value;
            const password = registerForm.querySelector('input[name="password"]').value;
            const confirmPassword = registerForm.querySelector('input[name="confirm_password"]').value;

            if (username.trim() === '' || password.trim() === '' || confirmPassword.trim() === '') {
                alert('All fields are required.');
                event.preventDefault();
            } else if (password !== confirmPassword) {
                alert('Passwords do not match.');
                event.preventDefault();
            }
        });
    }

    const addFilmForm = document.querySelector('form[action="add_film.php"]');
    if (addFilmForm) {
        addFilmForm.addEventListener('submit', function(event) {
            const title = addFilmForm.querySelector('input[name="title"]').value;
            const description = addFilmForm.querySelector('textarea[name="description"]').value;
            const releaseYear = addFilmForm.querySelector('input[name="release_year"]').value;
            const posterUrl = addFilmForm.querySelector('input[name="poster_url"]').value;

            if (title.trim() === '' || description.trim() === '' || releaseYear.trim() === '' || posterUrl.trim() === '') {
                alert('All film fields are required.');
                event.preventDefault();
            } else if (isNaN(releaseYear) || parseInt(releaseYear) < 1800 || parseInt(releaseYear) > 2100) { // Basic year validation
                alert('Please enter a valid release year (e.g., 2024).');
                event.preventDefault();
            }
        });
    }

    const reviewForm = document.querySelector('form[action="film.php"]'); // For review submission
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(event) {
            const rating = reviewForm.querySelector('input[name="rating"]').value;
            const comment = reviewForm.querySelector('textarea[name="comment"]').value;

            if (rating === '' || comment.trim() === '') {
                alert('Rating and comment are required.');
                event.preventDefault();
            } else if (parseInt(rating) < 1 || parseInt(rating) > 5) {
                alert('Rating must be between 1 and 5.');
                event.preventDefault();
            }
        });
    }
});