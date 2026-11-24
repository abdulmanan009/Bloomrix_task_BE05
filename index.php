<?php

// ========================================================
// 1. BOOK CLASS
// ========================================================
class Book {
    // Private properties (Encapsulation)
    private $id;
    private $title;
    private $author;
    private $isbn;
    private $isAvailable;

    // Constructor to initialize book properties
    public function __construct($id, $title, $author, $isbn) {
        $this->id          = $id;
        $this->title       = $title;
        $this->author      = $author;
        $this->isbn        = $isbn;
        $this->isAvailable = true; // By default, book is available
    }

    // --- Getters ---
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function isAvailable() {
        return $this->isAvailable;
    }

    // --- Setters ---
    public function setAvailability($status) {
        $this->isAvailable = (bool) $status;
    }
}


// ========================================================
// 2. MEMBER CLASS (Parent Class)
// ========================================================
class Member {
    // Protected so child classes (like PremiumMember) can access them
    protected $id;
    protected $name;
    protected $email;
    protected $membershipNumber;

    public function __construct($id, $name, $email, $membershipNumber) {
        $this->id               = $id;
        $this->name             = $name;
        $this->email            = $email;
        $this->membershipNumber = $membershipNumber;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    // Method to return member type
    public function getMemberType() {
        return "Regular Member";
    }
}


// ========================================================
// 3. PREMIUM MEMBER CLASS (Inheritance & Method Overriding)
// ========================================================
class PremiumMember extends Member {
    private $perk;

    public function __construct($id, $name, $email, $membershipNumber, $perk = "Free E-Book Access") {
        // Call the parent class constructor
        parent::__construct($id, $name, $email, $membershipNumber);
        $this->perk = $perk;
    }

    // Method Overriding: Overriding getMemberType() from Member class
    public function getMemberType() {
        return "Premium Member (Perk: " . $this->perk . ")";
    }
}


// ========================================================
// 4. LIBRARY CLASS (Manages Books and Members)
// ========================================================
class Library {
    private $books   = [];
    private $members = [];

    // Add a book to library
    public function addBook(Book $book) {
        $this->books[] = $book;
    }

    // Register a member
    public function registerMember(Member $member) {
        $this->members[] = $member;
    }

    // Display all books
    public function getAllBooks() {
        return $this->books;
    }

    // Search book by ID or Title
    public function searchBook($searchTerm) {
        $results = [];
        $searchTerm = strtolower(trim($searchTerm));

        foreach ($this->books as $book) {
            if (
                $book->getId() == $searchTerm ||
                strpos(strtolower($book->getTitle()), $searchTerm) !== false
            ) {
                $results[] = $book;
            }
        }
        return $results;
    }

    // Issue a book to a member
    public function issueBook($bookId, $memberId) {
        foreach ($this->books as $book) {
            if ($book->getId() == $bookId) {
                if ($book->isAvailable()) {
                    $book->setAvailability(false);
                    return "Success: Book '" . $book->getTitle() . "' has been issued to Member ID: $memberId.";
                } else {
                    return "Error: Book '" . $book->getTitle() . "' is already issued!";
                }
            }
        }
        return "Error: Book ID $bookId not found.";
    }

    // Return an issued book
    public function returnBook($bookId) {
        foreach ($this->books as $book) {
            if ($book->getId() == $bookId) {
                if (!$book->isAvailable()) {
                    $book->setAvailability(true);
                    return "Success: Book '" . $book->getTitle() . "' has been returned and is now available.";
                } else {
                    return "Notice: Book '" . $book->getTitle() . "' was not issued.";
                }
            }
        }
        return "Error: Book ID $bookId not found.";
    }

    // Get only available books
    public function getAvailableBooks() {
        $available = [];
        foreach ($this->books as $book) {
            if ($book->isAvailable()) {
                $available[] = $book;
            }
        }
        return $available;
    }

    // Get only issued books
    public function getIssuedBooks() {
        $issued = [];
        foreach ($this->books as $book) {
            if (!$book->isAvailable()) {
                $issued[] = $book;
            }
        }
        return $issued;
    }

    public function getAllMembers() {
        return $this->members;
    }
}


// ========================================================
// 5. DEMO EXECUTION (Creating Objects & Testing Methods)
// ========================================================

// 1. Create Library Object
$library = new Library();

// 2. Add Books
$library->addBook(new Book(1, "PHP & MySQL Web Development", "Luke Welling", "978-0321833891"));
$library->addBook(new Book(2, "Clean Code", "Robert C. Martin", "978-0132350884"));
$library->addBook(new Book(3, "Design Patterns in PHP", "William Sanders", "978-1449344856"));
$library->addBook(new Book(4, "The Pragmatic Programmer", "Andrew Hunt", "978-0201616224"));

// 3. Register Members (Regular and Premium)
$regularMember = new Member(101, "Ali Hassan", "ali@example.com", "MEM-101");
$premiumMember = new PremiumMember(102, "Sara Khan", "sara@example.com", "MEM-102", "Unlimited Downloads");

$library->registerMember($regularMember);
$library->registerMember($premiumMember);

// 4. Issue a Book
$issueMessage = $library->issueBook(2, 101); // Issue Book 2 ("Clean Code") to Member 101

// 5. Search for a Book demo
$searchResults = $library->searchBook("PHP");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Management System (OOP)</title>
</head>
<body>

    <h1>Library Management System (PHP OOP)</h1>

    <!-- --------------------------------------- -->
    <!-- REGISTERED MEMBERS                      -->
    <!-- --------------------------------------- -->
    <h2>Registered Members (Inheritance Demo)</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Membership Type</th>
        </tr>
        <?php foreach ($library->getAllMembers() as $member) { ?>
            <tr>
                <td><?php echo $member->getId(); ?></td>
                <td><?php echo $member->getName(); ?></td>
                <td><?php echo $member->getMemberType(); ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- --------------------------------------- -->
    <!-- ACTION LOG (ISSUE BOOK TEST)            -->
    <!-- --------------------------------------- -->
    <h2>Issue Book Action</h2>
    <p><strong><?php echo $issueMessage; ?></strong></p>

    <!-- --------------------------------------- -->
    <!-- SEARCH RESULT                           -->
    <!-- --------------------------------------- -->
    <h2>Search Book Demo (Search term: 'PHP')</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
        </tr>
        <?php foreach ($searchResults as $book) { ?>
            <tr>
                <td><?php echo $book->getId(); ?></td>
                <td><?php echo $book->getTitle(); ?></td>
                <td><?php echo $book->getAuthor(); ?></td>
                <td><?php echo $book->isAvailable() ? "Available" : "Issued"; ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- --------------------------------------- -->
    <!-- ALL BOOKS STATUS                        -->
    <!-- --------------------------------------- -->
    <h2>All Books in Library</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Status</th>
        </tr>
        <?php foreach ($library->getAllBooks() as $book) { ?>
            <tr>
                <td><?php echo $book->getId(); ?></td>
                <td><?php echo $book->getTitle(); ?></td>
                <td><?php echo $book->getAuthor(); ?></td>
                <td><?php echo $book->getIsbn(); ?></td>
                <td>
                    <?php if ($book->isAvailable()) { ?>
                        <span style="color: green;">Available</span>
                    <?php } else { ?>
                        <span style="color: red;">Issued</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- --------------------------------------- -->
    <!-- SUMMARY: AVAILABLE VS ISSUED            -->
    <!-- --------------------------------------- -->
    <h2>Available Books (<?php echo count($library->getAvailableBooks()); ?>)</h2>
    <ul>
        <?php foreach ($library->getAvailableBooks() as $book) { ?>
            <li><?php echo $book->getTitle(); ?> by <?php echo $book->getAuthor(); ?></li>
        <?php } ?>
    </ul>

    <h2>Issued Books (<?php echo count($library->getIssuedBooks()); ?>)</h2>
    <ul>
        <?php foreach ($library->getIssuedBooks() as $book) { ?>
            <li><?php echo $book->getTitle(); ?> (Issued)</li>
        <?php } ?>
    </ul>

</body>
</html>