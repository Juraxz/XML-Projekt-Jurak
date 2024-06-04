<?php
$moviesJson = file_get_contents('movies.json');
$movies = json_decode($moviesJson, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Library</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <h1 id="title">Movie Checklist</h1>
        <nav>
            <ul>
                <li><a href="index.php"><i class="fas fa-book"></i> Books</a></li>
                <li><a href="filmovi.php"><i class="fas fa-film"></i> Movies</a></li>
            </ul>
        </nav>
        <input type="text" id="search-movies" placeholder="Search for movies...">
    </header>
    <main>
        <div id="movie-list" class="movie-list">
            <?php foreach ($movies as $movie): ?>
                <div class="movie" data-id="<?php echo $movie['id']; ?>">
                    <img src="<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?>">
                    <div class="movie-details">
                        <h2><?php echo $movie['title']; ?></h2>
                        <p><?php echo $movie['description']; ?></p>
                        <div class="watched-container">
                            <input type="checkbox" class="watched-checkbox" id="watched-<?php echo $movie['id']; ?>" <?php echo isset($_COOKIE['watched_' . $movie['id']]) ? 'checked' : ''; ?>>
                            <label for="watched-<?php echo $movie['id']; ?>"></label>
                            <span class="watched-text"><?php echo isset($_COOKIE['watched_' . $movie['id']]) ? 'Watched' : ''; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <footer>
        <p>Damjan Jurak, 2024., XML-Programiranje</p>
    </footer>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.watched-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const movieId = checkbox.id.replace('watched-', '');
                if (checkbox.checked) {
                    localStorage.setItem('watched_' + movieId, 'true');
                    checkbox.nextElementSibling.textContent = 'Watched';
                } else {
                    localStorage.removeItem('watched_' + movieId);
                    checkbox.nextElementSibling.textContent = '';
                }
            });

            const movieId = checkbox.id.replace('watched-', '');
            if (localStorage.getItem('watched_' + movieId)) {
                checkbox.checked = true;
                checkbox.nextElementSibling.textContent = 'Watched';
            }
        });
        
        const searchInput = document.getElementById('search-movies');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            document.querySelectorAll('.movie').forEach(movieElement => {
                const title = movieElement.querySelector('h2').textContent.toLowerCase();
                if (title.includes(query)) {
                    movieElement.style.display = 'block';
                } else {
                    movieElement.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>


