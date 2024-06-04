<?php
$booksJson = file_get_contents('books.json');
$books = json_decode($booksJson, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Library</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header>
    <h1 id="title">Book Library</h1>
    <nav>
        <ul>
            <li><a href="index.php"><i class="fas fa-book"></i> Books</a></li>
            <li><a href="movies.php"><i class="fas fa-film"></i> Movies</a></li>
        </ul>
    </nav>
    <input type="text" id="search" placeholder="Search for books...">
</header>
<main>
    <div id="rated-books-container" style="display: none;">
        <h2 id="rated-books-header">Rated Books (0)</h2>
        <div id="rated-books-list" class="book-list"></div>
    </div>
    <div id="book-list" class="book-list">
        <?php foreach ($books as $book): ?>
            <div class="book<?php echo isset($book['rating']) ? ' rated' : ''; ?>" data-id="<?php echo $book['id']; ?>" data-rating="<?php echo isset($book['rating']) ? $book['rating'] : ''; ?>">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                <div class="book-details">
                    <h2><?php echo $book['title']; ?></h2>
                    <p><?php echo $book['author']; ?> (<?php echo $book['year']; ?>)</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="book-details" class="hidden">
        <button id="close-details">Close</button>
        <img id="details-image" src="" alt="">
        <div class="book-details">
            <h2 id="details-title"></h2>
            <p id="details-author"></p>
            <p id="details-year"></p>
            <p id="details-description"></p>
            <p id="details-rating"></p>
        </div>
        <label for="rating">Rate this book:</label>
        <input type="number" id="rating" min="1" max="5">
        <button id="submit-rating">Submit</button>
        <button id="compare-button" class="hidden">Compare</button>
    </div>
    <div id="comparison-window" class="hidden">
        <div id="comparison-content"></div>
        <button id="close-comparison">Close</button>
    </div>
</main>
<footer>
    <p>Damjan Jurak, 2024., XML-Programiranje</p>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const books = <?php echo json_encode($books); ?>;
    const bookList = document.getElementById('book-list');
    const bookDetails = document.getElementById('book-details');
    const searchInput = document.getElementById('search');
    const detailsImage = document.getElementById('details-image');
    const detailsTitle = document.getElementById('details-title');
    const detailsAuthor = document.getElementById('details-author');
    const detailsYear = document.getElementById('details-year');
    const detailsDescription = document.getElementById('details-description');
    const detailsRating = document.getElementById('details-rating');
    const ratingInput = document.getElementById('rating');
    const submitRatingButton = document.getElementById('submit-rating');
    const compareButton = document.getElementById('compare-button');
    const comparisonWindow = document.getElementById('comparison-window');
    const comparisonContent = document.getElementById('comparison-content');
    const closeComparisonButton = document.getElementById('close-comparison');
    const ratedBooksContainer = document.getElementById('rated-books-container');
    const ratedBooksList = document.getElementById('rated-books-list');
    const ratedBooksHeader = document.getElementById('rated-books-header');

    let selectedBook;
    let selectedBooksForComparison = [];

    function loadRatings() {
        const storedBooks = JSON.parse(localStorage.getItem('books'));
        if (storedBooks) {
            books.forEach(book => {
                const storedBook = storedBooks.find(stored => stored.id === book.id);
                if (storedBook && storedBook.rating) {
                    book.rating = storedBook.rating;
                }
            });
        }
    }

    function saveRatings() {
        localStorage.setItem('books', JSON.stringify(books));
    }

    loadRatings();

    submitRatingButton.addEventListener('click', () => {
        const newRating = parseFloat(ratingInput.value);
        if (newRating >= 1 && newRating <= 5 && selectedBook) {
            selectedBook.rating = newRating;
            detailsRating.textContent = `Rating: ${newRating}`;
            saveRatings();

            const bookElement = document.querySelector(`.book[data-id="${selectedBook.id}"]`);
            bookElement.style.backgroundColor = '#c1e9c1';
            bookElement.classList.add('rated');
            bookElement.dataset.rating = newRating;

            bookList.removeChild(bookElement);
            ratedBooksList.appendChild(bookElement);
            ratedBooksContainer.style.display = 'block';
            ratedBooksHeader.textContent = `Rated Books (${ratedBooksList.children.length})`;
        } else {
            alert('Please enter a valid rating between 1 and 5.');
        }
    });

    ratedBooksList.addEventListener('click', (e) => {
        const ratedBookElement = e.target.closest('.book');
        if (!ratedBookElement) return;

        const confirmation = confirm('Are you sure you want to delete the rating?');
        if (confirmation) {
            const bookId = ratedBookElement.dataset.id;
            const foundBook = books.find(book => book.id == bookId);
            if (foundBook) {
                foundBook.rating = undefined;
            }
            ratedBookElement.classList.remove('rated');
            ratedBookElement.dataset.rating = '';
            ratedBookElement.style.backgroundColor = '';
            bookList.appendChild(ratedBookElement);
            ratedBooksContainer.style.display = ratedBooksList.children.length > 0 ? 'block' : 'none';
            ratedBooksHeader.textContent = `Rated Books (${ratedBooksList.children.length})`;
            saveRatings();
        }
    });

    function loadBookDetails(book) {
        detailsImage.src = book.image;
        detailsTitle.textContent = book.title;
        detailsAuthor.textContent = book.author;
        detailsYear.textContent = `Year: ${book.year}`;
        detailsDescription.textContent = book.description;
        detailsRating.textContent = `Rating: ${book.rating || 'Not rated'}`;
    }

    bookList.addEventListener('click', (e) => {
        const bookElement = e.target.closest('.book');
        if (!bookElement) return;

        const bookId = bookElement.dataset.id;
        selectedBook = books.find(book => book.id == bookId);

        if (selectedBook) {
            loadBookDetails(selectedBook);
            bookDetails.classList.remove('hidden');
            comparisonWindow.classList.add('hidden');
        }
    });

    document.getElementById('close-details').addEventListener('click', () => {
        bookDetails.classList.add('hidden');
    });

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll('.book').forEach(bookElement => {
            const title = bookElement.querySelector('h2').textContent.toLowerCase();
            if (title.includes(query)) {
                bookElement.style.display = 'block';
            } else {
                bookElement.style.display = 'none';
            }
        });
    });

    // Refreshing the displayed books based on their ratings
    renderRatedBooks();
});

</script>
</body>
</html>

