-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2023 at 01:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AdminApproval` (IN `p_borrow_id` INT, OUT `p_result` VARCHAR(255))   BEGIN
    DECLARE book_status VARCHAR(50);
    SELECT status INTO book_status FROM issued_books WHERE borrow_id = p_borrow_id;

    IF book_status = 'Pending' THEN
        UPDATE issued_books SET status = 'Approved' WHERE borrow_id = p_borrow_id;
        SET p_result = 'Book approved successfully.';
    ELSE
        SET p_result = 'Error: Book is not pending approval.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `BorrowBook` (IN `p_user_id` INT, IN `p_book_id` INT, OUT `p_result` VARCHAR(255))   BEGIN
    DECLARE available_quantity INT;

    SELECT book_quantity INTO available_quantity FROM books WHERE book_id = p_book_id;

    IF available_quantity > 0 THEN
        START TRANSACTION;

        INSERT INTO issued_books (user_id, book_id, issue_date, status)
        VALUES (p_user_id, p_book_id, CURDATE(), 'Pending'); 

        UPDATE books SET book_quantity = book_quantity - 1 WHERE book_id = p_book_id;

        COMMIT;

        SET p_result = 'Book borrowed successfully. Pending admin approval.';
    ELSE
        SET p_result = 'Error: Book not available.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteAuthor` (IN `p_author_id` INT)   BEGIN
    DELETE FROM authors WHERE author_id = p_author_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteCategory` (IN `p_catId` INT)   BEGIN
    DELETE FROM categories WHERE cat_id = p_catId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_book` (IN `bookId` INT)   BEGIN
    DELETE FROM books WHERE book_id = bookId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertAuthor` (IN `authorName` VARCHAR(255))   BEGIN
    INSERT INTO authors (author_name) VALUES (authorName);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertCategory` (IN `p_category_name` VARCHAR(255))   BEGIN
    INSERT INTO categories (category_name)
    VALUES (p_category_name);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_book` (IN `p_book_name` VARCHAR(255), IN `p_author_id` INT, IN `p_cat_id` INT, IN `p_ISBN` VARCHAR(255), IN `p_book_quantity` INT)   BEGIN
    
    INSERT INTO books (book_name, author_id, cat_id, ISBN, book_quantity)
    VALUES (p_book_name, p_author_id, p_cat_id, p_ISBN, p_book_quantity);

    SELECT 'Book added successfully' AS result;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ReturnBook` (IN `p_borrow_id` INT, OUT `p_result` VARCHAR(255))   BEGIN
    DECLARE issued_status VARCHAR(20);
    DECLARE v_book_id INT;
    DECLARE v_user_id INT;

    SELECT status, book_id, user_id INTO issued_status, v_book_id, v_user_id FROM issued_books WHERE borrow_id = p_borrow_id;

    IF issued_status IS NOT NULL THEN
        START TRANSACTION;

       UPDATE issued_books SET status = 'Returned', return_date = CURDATE() WHERE borrow_id = p_borrow_id;      
        UPDATE books SET book_quantity = book_quantity + 1 WHERE book_id = v_book_id;

        COMMIT;

        SET p_result = 'Book returned successfully.';
    ELSE
        SET p_result = 'Error: Issued book not found.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectAllAuthors` ()   BEGIN
    SELECT * FROM authors;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectAllBooks` ()   BEGIN

    SELECT books.book_id, books.book_name, books.ISBN,  authors.author_name, categories.category_name
    FROM
        books 
JOIN authors 
ON books.author_id = authors.author_id
JOIN categories 
 ON books.cat_id = categories.cat_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectAllCategories` ()   BEGIN
    SELECT * FROM categories;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateAuthor` (IN `p_author_id` INT, IN `p_author_name` VARCHAR(255))   BEGIN
    UPDATE authors SET author_name = p_author_name WHERE author_id = p_author_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateBook` (IN `p_book_id` INT, IN `p_book_name` VARCHAR(255), IN `p_author_id` INT, IN `p_cat_id` INT, IN `p_ISBN` VARCHAR(20), IN `p_book_quantity` INT)   BEGIN

    IF (SELECT COUNT(*) FROM books WHERE book_id = p_book_id) > 0 THEN

        UPDATE books
        SET
            book_name = p_book_name,
            author_id = p_author_id,
            cat_id = p_cat_id,
            ISBN = p_ISBN,
            book_quantity = p_book_quantity
        WHERE book_id = p_book_id;

        SELECT 'Book updated successfully' AS message;
    ELSE
        SELECT 'Error: Book not found' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateCategory` (IN `p_cat_id` INT, IN `p_category_name` VARCHAR(255))   BEGIN
    UPDATE categories
    SET category_name = p_category_name
    WHERE cat_id = p_cat_id;
    
    SELECT 'Category updated successfully.' AS message;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `mobile` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `mobile`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin@1234', 1148458757);

-- --------------------------------------------------------

--
-- Table structure for table `audit_delete`
--

CREATE TABLE `audit_delete` (
  `audit_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `book_name` varchar(255) DEFAULT NULL,
  `book_quantity` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `action_type` varchar(20) DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `action_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_delete`
--

INSERT INTO `audit_delete` (`audit_id`, `book_id`, `book_name`, `book_quantity`, `author_id`, `cat_id`, `ISBN`, `action_type`, `action_timestamp`, `action_description`) VALUES
(1, 9, 'rtyrtyry', 0, 2, 1, '454564', 'DELETE', '2023-12-27 12:19:26', 'Book deleted'),
(4, 14, 'dyan lang', 280, 2, 1, '64564', 'DELETE', '2023-12-27 12:21:33', 'Book deleted'),
(7, 16, 'Learn English in 1day', 160, 5, 2, '675765', 'DELETE', '2023-12-29 07:06:49', 'Book deleted');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `author_name`) VALUES
(1, 'Joyce Calvez'),
(2, 'Kelly Ann Alinsub'),
(3, 'Brian Agraviador'),
(4, 'JB Locsin'),
(5, 'Joyce Ann Calvez'),
(6, 'Dito lang');

-- --------------------------------------------------------

--
-- Stand-in structure for view `author_view`
-- (See below for the actual view)
--
CREATE TABLE `author_view` (
`author_id` int(11)
,`author_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `book_quantity` int(11) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ISBN` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `book_quantity`, `author_id`, `cat_id`, `ISBN`) VALUES
(2, 'Experiment 101', 221, 2, 2, '12312'),
(3, 'Alamat ng Tubig', 160, 3, 2, '456546'),
(5, 'Spiderman', 10, 2, 1, '867678'),
(6, 'Experiment 101', 107, 1, 2, '657657'),
(10, 'alamat ng pinya\r\n', 98, 1, 1, '23131'),
(13, 'Juan Tamad', 98, 2, 1, '5345354'),
(15, 'Learn English in 1day', 152, 5, 2, '675765');

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `after_book_insert` AFTER INSERT ON `books` FOR EACH ROW BEGIN
    INSERT INTO insert_audit (book_id, book_name, book_quantity, author_id, cat_id, ISBN, action_type, action_description)
    VALUES (NEW.book_id, NEW.book_name, NEW.book_quantity, NEW.author_id, NEW.cat_id, NEW.ISBN, 'INSERT', 'New book added.');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_book_update` AFTER UPDATE ON `books` FOR EACH ROW BEGIN
    INSERT INTO update_audit (book_id, book_name, book_quantity, author_id, cat_id, ISBN, action_type, action_description)
    VALUES (NEW.book_id, NEW.book_name, NEW.book_quantity, NEW.author_id, NEW.cat_id, NEW.ISBN, 'UPDATE', 'Book information updated.');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `books_delete_trigger` BEFORE DELETE ON `books` FOR EACH ROW BEGIN
    INSERT INTO audit_delete (book_id, book_name, book_quantity, author_id, cat_id, ISBN, action_type, action_description)
    VALUES (OLD.book_id, OLD.book_name, OLD.book_quantity, OLD.author_id, OLD.cat_id, OLD.ISBN, 'DELETE', 'Book deleted');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `book_view`
-- (See below for the actual view)
--
CREATE TABLE `book_view` (
`book_id` int(11)
,`book_name` varchar(255)
,`ISBN` varchar(13)
);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `category_name`) VALUES
(1, 'Science'),
(2, 'Math'),
(9, 'Filipino'),
(11, 'English'),
(12, 'Science'),
(14, 'Hekasi');

-- --------------------------------------------------------

--
-- Stand-in structure for view `category_view`
-- (See below for the actual view)
--
CREATE TABLE `category_view` (
`cat_id` int(11)
,`category_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `insert_audit`
--

CREATE TABLE `insert_audit` (
  `audit_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `book_name` varchar(255) DEFAULT NULL,
  `book_quantity` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `action_type` varchar(10) DEFAULT NULL,
  `action_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `insert_audit`
--

INSERT INTO `insert_audit` (`audit_id`, `book_id`, `book_name`, `book_quantity`, `author_id`, `cat_id`, `ISBN`, `action_type`, `action_description`, `created_at`) VALUES
(1, 13, 'Juan Tamad', 100, 2, 1, '5345354', 'INSERT', 'New book added.', '2023-12-27 12:18:22'),
(2, 14, 'Spiderman', 160, 2, 1, '5345354', 'INSERT', 'New book added.', '2023-12-27 12:18:38'),
(3, 15, 'Learn English in 1day', 160, 5, 2, '675765', 'INSERT', 'New book added.', '2023-12-29 00:41:37'),
(4, 16, 'Learn English in 1day', 160, 5, 2, '675765', 'INSERT', 'New book added.', '2023-12-29 07:06:37');

-- --------------------------------------------------------

--
-- Table structure for table `issued_books`
--

CREATE TABLE `issued_books` (
  `borrow_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issued_books`
--

INSERT INTO `issued_books` (`borrow_id`, `user_id`, `book_id`, `issue_date`, `return_date`, `status`) VALUES
(1, 1, 6, '2023-12-27', NULL, 'Pending'),
(2, 8, 10, '2023-12-27', NULL, 'Approved'),
(3, 1, 10, '2023-12-27', '2023-12-27', 'Returned'),
(4, 8, 6, '2023-12-27', NULL, 'Approved'),
(5, 1, 13, '2023-12-29', NULL, 'Approved'),
(6, 1, 15, '2023-12-29', NULL, 'Approved'),
(7, 1, 15, '2023-12-29', NULL, 'Approved'),
(8, 1, 15, '2023-12-29', '2023-12-29', 'Returned'),
(9, 1, 13, '2023-12-29', NULL, 'Approved'),
(10, 8, 15, '2023-12-29', NULL, 'Pending'),
(11, 8, 15, '2023-12-29', NULL, 'Approved'),
(12, 8, 15, '2023-12-29', NULL, 'Pending'),
(13, 8, 15, '2023-12-29', '2023-12-29', 'Returned'),
(14, 8, 15, '2023-12-29', '2023-12-29', 'Returned'),
(15, 8, 15, '2023-12-29', NULL, 'Pending'),
(16, 8, 15, '2023-12-29', NULL, 'Approved'),
(17, 8, 15, '2023-12-29', NULL, 'Pending'),
(18, 8, 15, '2023-12-29', NULL, 'Pending'),
(19, 8, 15, '2023-12-29', '2023-12-29', 'Returned');

-- --------------------------------------------------------

--
-- Table structure for table `log_table`
--

CREATE TABLE `log_table` (
  `log_id` int(11) NOT NULL,
  `log_message` text DEFAULT NULL,
  `log_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_table`
--

INSERT INTO `log_table` (`log_id`, `log_message`, `log_timestamp`) VALUES
(1, 'Input borrow_id: 2', '2023-12-27 09:30:37'),
(2, 'Status before update: Input borrow_id: 2', '2023-12-27 09:30:37'),
(3, 'Error: Book approval failed. The book may have already been approved or rejected.', '2023-12-27 09:30:37'),
(4, 'Input borrow_id: 1', '2023-12-27 09:30:47'),
(5, 'Status before update: Input borrow_id: 1', '2023-12-27 09:30:47'),
(6, 'Error: Book approval failed. The book may have already been approved or rejected.', '2023-12-27 09:30:47');

-- --------------------------------------------------------

--
-- Table structure for table `update_audit`
--

CREATE TABLE `update_audit` (
  `audit_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `book_name` varchar(255) DEFAULT NULL,
  `book_quantity` int(155) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ISBN` varchar(255) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `action_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `update_audit`
--

INSERT INTO `update_audit` (`audit_id`, `book_id`, `book_name`, `book_quantity`, `author_id`, `cat_id`, `ISBN`, `action_type`, `action_timestamp`, `action_description`) VALUES
(2, 2, 'Experiment 101', 221, 2, 2, '12312', 'UPDATE', '2023-12-27 12:24:34', 'Book information updated.'),
(3, 13, 'Juan Tamad', 99, 2, 1, '5345354', 'UPDATE', '2023-12-29 00:57:06', 'Book information updated.'),
(4, 15, 'Learn English in 1day', 159, 5, 2, '675765', 'UPDATE', '2023-12-29 01:39:43', 'Book information updated.'),
(5, 15, 'Learn English in 1day', 158, 5, 2, '675765', 'UPDATE', '2023-12-29 01:42:49', 'Book information updated.'),
(6, 15, 'Learn English in 1day', 157, 5, 2, '675765', 'UPDATE', '2023-12-29 01:44:05', 'Book information updated.'),
(7, 13, 'Juan Tamad', 98, 2, 1, '5345354', 'UPDATE', '2023-12-29 01:45:09', 'Book information updated.'),
(8, 15, 'Learn English in 1day', 156, 5, 2, '675765', 'UPDATE', '2023-12-29 06:32:09', 'Book information updated.'),
(9, 15, 'Learn English in 1day', 155, 5, 2, '675765', 'UPDATE', '2023-12-29 06:35:39', 'Book information updated.'),
(10, 15, 'Learn English in 1day', 154, 5, 2, '675765', 'UPDATE', '2023-12-29 06:39:10', 'Book information updated.'),
(11, 15, 'Learn English in 1day', 153, 5, 2, '675765', 'UPDATE', '2023-12-29 06:41:14', 'Book information updated.'),
(12, 15, 'Learn English in 1day', 152, 5, 2, '675765', 'UPDATE', '2023-12-29 06:44:51', 'Book information updated.'),
(13, 15, 'Learn English in 1day', 151, 5, 2, '675765', 'UPDATE', '2023-12-29 06:50:13', 'Book information updated.'),
(14, 15, 'Learn English in 1day', 152, 5, 2, '675765', 'UPDATE', '2023-12-29 06:57:33', 'Book information updated.'),
(15, 15, 'Learn English in 1day', 153, 5, 2, '675765', 'UPDATE', '2023-12-29 06:59:50', 'Book information updated.'),
(16, 15, 'Learn English in 1day', 154, 5, 2, '675765', 'UPDATE', '2023-12-29 07:04:13', 'Book information updated.'),
(17, 15, 'Learn English in 1day', 155, 5, 2, '675765', 'UPDATE', '2023-12-29 07:04:27', 'Book information updated.'),
(18, 15, 'Learn English in 1day', 154, 5, 2, '675765', 'UPDATE', '2023-12-29 07:04:50', 'Book information updated.'),
(19, 3, 'Alamat ng Tubig', 160, 3, 2, '456546', 'UPDATE', '2023-12-29 07:06:02', 'Book information updated.'),
(20, 15, 'Learn English in 1day', 153, 5, 2, '675765', 'UPDATE', '2023-12-29 12:25:27', 'Book information updated.'),
(21, 15, 'Learn English in 1day', 152, 5, 2, '675765', 'UPDATE', '2023-12-29 12:32:27', 'Book information updated.'),
(22, 15, 'Learn English in 1day', 151, 5, 2, '675765', 'UPDATE', '2023-12-29 12:32:36', 'Book information updated.'),
(23, 15, 'Learn English in 1day', 152, 5, 2, '675765', 'UPDATE', '2023-12-29 12:33:24', 'Book information updated.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `student_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `mobile` int(10) NOT NULL,
  `address` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `student_name`, `email`, `password`, `mobile`, `address`) VALUES
(1, 'joyjoy', 'joyjoy@gmail.com', '123456789', 912311231, 'Dyan lang\r\n'),
(8, 'Jb Locsin', 'jbreylocsin@gmail.com', '123123123', 678768123, 'maligaya saglit');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_books_author_category`
-- (See below for the actual view)
--
CREATE TABLE `view_books_author_category` (
`book_id` int(11)
,`book_name` varchar(255)
,`book_quantity` int(11)
,`ISBN` varchar(13)
,`category_name` varchar(255)
,`author_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_issued_books`
-- (See below for the actual view)
--
CREATE TABLE `view_issued_books` (
`borrow_id` int(11)
,`student_name` varchar(50)
,`book_name` varchar(255)
,`issue_date` date
,`return_date` date
,`status` varchar(20)
);

-- --------------------------------------------------------

--
-- Structure for view `author_view`
--
DROP TABLE IF EXISTS `author_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `author_view`  AS SELECT `authors`.`author_id` AS `author_id`, `authors`.`author_name` AS `author_name` FROM `authors` ;

-- --------------------------------------------------------

--
-- Structure for view `book_view`
--
DROP TABLE IF EXISTS `book_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_view`  AS SELECT `books`.`book_id` AS `book_id`, `books`.`book_name` AS `book_name`, `books`.`ISBN` AS `ISBN` FROM `books` ;

-- --------------------------------------------------------

--
-- Structure for view `category_view`
--
DROP TABLE IF EXISTS `category_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `category_view`  AS SELECT `categories`.`cat_id` AS `cat_id`, `categories`.`category_name` AS `category_name` FROM `categories` ;

-- --------------------------------------------------------

--
-- Structure for view `view_books_author_category`
--
DROP TABLE IF EXISTS `view_books_author_category`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_books_author_category`  AS SELECT `b`.`book_id` AS `book_id`, `b`.`book_name` AS `book_name`, `b`.`book_quantity` AS `book_quantity`, `b`.`ISBN` AS `ISBN`, `c`.`category_name` AS `category_name`, `a`.`author_name` AS `author_name` FROM ((`books` `b` join `authors` `a` on(`b`.`author_id` = `a`.`author_id`)) join `categories` `c` on(`b`.`cat_id` = `c`.`cat_id`)) ORDER BY `b`.`book_id` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `view_issued_books`
--
DROP TABLE IF EXISTS `view_issued_books`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_issued_books`  AS SELECT `issued_books`.`borrow_id` AS `borrow_id`, `users`.`student_name` AS `student_name`, `books`.`book_name` AS `book_name`, `issued_books`.`issue_date` AS `issue_date`, `issued_books`.`return_date` AS `return_date`, `issued_books`.`status` AS `status` FROM ((`issued_books` join `users` on(`issued_books`.`user_id` = `users`.`user_id`)) join `books` on(`issued_books`.`book_id` = `books`.`book_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_delete`
--
ALTER TABLE `audit_delete`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `insert_audit`
--
ALTER TABLE `insert_audit`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `log_table`
--
ALTER TABLE `log_table`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `update_audit`
--
ALTER TABLE `update_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_delete`
--
ALTER TABLE `audit_delete`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `insert_audit`
--
ALTER TABLE `insert_audit`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `issued_books`
--
ALTER TABLE `issued_books`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `log_table`
--
ALTER TABLE `log_table`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `update_audit`
--
ALTER TABLE `update_audit`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE;

--
-- Constraints for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD CONSTRAINT `issued_books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `issued_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `update_audit`
--
ALTER TABLE `update_audit`
  ADD CONSTRAINT `update_audit_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `update_audit_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`author_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `update_audit_ibfk_3` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
