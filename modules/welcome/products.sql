SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`id`, `product_name`, `price`, `stock`, `created_at`) VALUES
(1, 'Omega', '699.99', 40, '2025-10-28 09:58:53'),
(2, 'Rolex', '449.99', 35, '2025-10-28 09:58:53'),
(3, 'Casio', '299.99', 60, '2025-10-28 09:58:53'),
(4, 'Patek', '149.99', 75, '2025-10-28 09:58:53'),
(5, 'Vacheron', '19.99', 100, '2025-10-28 09:58:53');


ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
