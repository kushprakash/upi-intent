-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 15, 2026 at 10:08 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u223771646_demotry`
--

-- --------------------------------------------------------

--
-- Table structure for table `amazon_pay`
--

CREATE TABLE `amazon_pay` (
  `id` int(11) NOT NULL,
  `phoneNumber` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `upi_id` varchar(150) DEFAULT NULL,
  `cookie` text DEFAULT NULL,
  `status` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_token` varchar(120) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bharatpe_tokens`
--

CREATE TABLE `bharatpe_tokens` (
  `id` int(11) NOT NULL,
  `user_token` longtext DEFAULT NULL,
  `phoneNumber` varchar(255) DEFAULT NULL,
  `token` text DEFAULT NULL,
  `cookie` text DEFAULT NULL,
  `merchantId` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT 'Deactive',
  `Upiid` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `callback_report`
--

CREATE TABLE `callback_report` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `request_url` longtext NOT NULL,
  `response` longtext NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `callback_response`
--

CREATE TABLE `callback_response` (
  `id` int(11) NOT NULL,
  `response` text DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashfree_details`
--

CREATE TABLE `cashfree_details` (
  `ID` int(11) NOT NULL,
  `CLIENT_ID` text DEFAULT NULL,
  `SECRETKEY` text DEFAULT NULL,
  `IP` text DEFAULT NULL,
  `STATUS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `freecharge`
--

CREATE TABLE `freecharge` (
  `id` int(11) NOT NULL,
  `number` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_token` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `cookie` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `upi_id` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Deactive',
  `user_id` bigint(20) DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_request`
--

CREATE TABLE `fund_request` (
  `id` int(11) NOT NULL,
  `userid` varchar(150) DEFAULT NULL,
  `txn_id` varchar(100) DEFAULT NULL,
  `bankid` varchar(200) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `ref_id` varchar(100) DEFAULT NULL,
  `utr_no` varchar(100) DEFAULT NULL,
  `fund_type` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gpay_tokens`
--

CREATE TABLE `gpay_tokens` (
  `id` int(11) NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `cokkie` text DEFAULT NULL,
  `Upiid` varchar(100) DEFAULT NULL,
  `at` varchar(255) DEFAULT NULL,
  `f-req` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Deactive',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hdfc`
--

CREATE TABLE `hdfc` (
  `id` int(11) NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `seassion` varchar(255) DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `user_token` varchar(255) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `upi_hdfc` varchar(255) DEFAULT NULL,
  `UPI` varchar(255) DEFAULT NULL,
  `tidlist` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant`
--

CREATE TABLE `merchant` (
  `merchant_id` int(11) NOT NULL,
  `merchant_name` enum('PhonePe Business','SBI Merchant','Paytm Business','SmartHub','') NOT NULL,
  `merchant_username` varchar(100) NOT NULL,
  `merchant_password` varchar(100) NOT NULL,
  `merchant_primary` enum('Active','InActive') DEFAULT 'InActive',
  `merchant_payupi` enum('Show','Hide','') DEFAULT 'Show',
  `merchant_timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `merchant_session` text DEFAULT NULL,
  `merchant_csrftoken` text DEFAULT NULL,
  `merchant_token` text DEFAULT NULL,
  `user_token` text DEFAULT NULL,
  `merchant_qrdata` longtext DEFAULT NULL,
  `merchant_data` longtext DEFAULT NULL,
  `merchant_upi` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` enum('Active','InActive','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant_domains`
--

CREATE TABLE `merchant_domains` (
  `id` int(11) NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `domain_url` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant_ips`
--

CREATE TABLE `merchant_ips` (
  `id` int(11) NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant_webhooks`
--

CREATE TABLE `merchant_webhooks` (
  `id` int(11) NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `webhook_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobikwik_token`
--

CREATE TABLE `mobikwik_token` (
  `id` int(11) NOT NULL,
  `user_token` longtext DEFAULT NULL,
  `phoneNumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Authorization` varchar(255) DEFAULT NULL,
  `merchant_upi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `date` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Deactive',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `user_id` varchar(150) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` mediumtext NOT NULL,
  `user_token` longtext NOT NULL,
  `status` text NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `charge` double NOT NULL DEFAULT 0,
  `utr` longtext NOT NULL,
  `plan_id` longtext DEFAULT NULL,
  `customer_name` longtext DEFAULT NULL,
  `customer_mobile` longtext NOT NULL,
  `redirect_url` longtext NOT NULL,
  `payment_link` text DEFAULT NULL,
  `remark1` longtext NOT NULL,
  `remark2` longtext NOT NULL,
  `gateway_txn` longtext NOT NULL,
  `method` text NOT NULL,
  `HDFC_TXNID` mediumtext DEFAULT NULL,
  `upiLink` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `byteTransactionId` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `paytm_txn_ref` varchar(255) DEFAULT NULL,
  `bank_orderid` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `merchant_id` varchar(100) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `check_response` text DEFAULT NULL,
  `user_mode` varchar(10) NOT NULL DEFAULT '1',
  `reason` text DEFAULT NULL,
  `proof` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_links`
--

CREATE TABLE `payment_links` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `link_token` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nonce` varchar(255) DEFAULT NULL,
  `payee_vpa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paytm_tokens`
--

CREATE TABLE `paytm_tokens` (
  `id` int(11) NOT NULL,
  `user_token` longtext NOT NULL,
  `phoneNumber` varchar(255) DEFAULT NULL,
  `MID` varchar(255) DEFAULT NULL,
  `Upiid` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT 'Deactive',
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `paytm_tokens`
--

INSERT INTO `paytm_tokens` (`id`, `user_token`, `phoneNumber`, `MID`, `Upiid`, `created_at`, `date`, `status`, `user_id`) VALUES
(37, 'ecc45f65145f5948fc38bdb54d81eb6c', '9234456535', 'hSlQRm39331672626966', 'paytmqrnlbmx7u2cv@paytm', '2026-01-07 18:01:32', '2026-01-07 18:01:32', 'Active', 162);

-- --------------------------------------------------------

--
-- Table structure for table `phonepe_tokens`
--

CREATE TABLE `phonepe_tokens` (
  `sl` int(11) NOT NULL,
  `user_token` longtext NOT NULL,
  `phoneNumber` longtext NOT NULL,
  `userId` longtext NOT NULL,
  `token` longtext NOT NULL,
  `refreshToken` longtext NOT NULL,
  `name` text NOT NULL,
  `device_data` longtext NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Deactive',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plugins_list`
--

CREATE TABLE `plugins_list` (
  `id` int(11) NOT NULL,
  `user_id` varchar(200) DEFAULT NULL,
  `plugin_name` varchar(100) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `paid_status` varchar(20) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `popup_alert`
--

CREATE TABLE `popup_alert` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `img` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quintus_tokens`
--

CREATE TABLE `quintus_tokens` (
  `id` int(11) NOT NULL,
  `user_token` varchar(100) DEFAULT NULL,
  `phoneNumber` varchar(30) DEFAULT NULL,
  `Upiid` varchar(100) DEFAULT NULL,
  `accessToken` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Deactive',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quintus_tokens`
--

INSERT INTO `quintus_tokens` (`id`, `user_token`, `phoneNumber`, `Upiid`, `accessToken`, `status`, `date`, `user_id`) VALUES
(31, 'ecc45f65145f5948fc38bdb54d81eb6c', '9234456535', 'yespay.qtosse7w6kpb9y@yesbankltd', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE3Njc4MTAzNDQsImV4cCI6MTc3MDQwMjM0NCwiYXVkIjoiNjkxODQ3ODJlZGJhMTBjNTFiZjlhOTMzIiwiaXNzIjoicXVpbnR1c3RlY2guaW4ifQ.uHBwVufnxoOgQC0g-aDgesWf80rR_Z5UYQgb6QW9-Ns', 'Active', '2026-01-07 18:25:30', '162');

-- --------------------------------------------------------

--
-- Table structure for table `refer_history`
--

CREATE TABLE `refer_history` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `sponser_id` varchar(60) DEFAULT NULL,
  `refer_type` int(11) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reported_txn`
--

CREATE TABLE `reported_txn` (
  `id` int(11) NOT NULL,
  `order_id` varchar(100) DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `screenshot` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `transactionId` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT NULL,
  `order_id` mediumtext DEFAULT NULL,
  `vpa` mediumtext DEFAULT NULL,
  `paymentApp` mediumtext DEFAULT NULL,
  `amount` mediumtext DEFAULT NULL,
  `user_token` mediumtext DEFAULT NULL,
  `UTR` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `mobile` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `merchantTransactionId` varchar(255) DEFAULT NULL,
  `transactionNote` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_mode` varchar(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settlement`
--

CREATE TABLE `settlement` (
  `id` int(11) NOT NULL,
  `userid` varchar(150) DEFAULT NULL,
  `bankid` varchar(200) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `ref_id` varchar(100) DEFAULT NULL,
  `tid` varchar(100) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `check_response` text DEFAULT NULL,
  `utr_no` varchar(100) DEFAULT NULL,
  `s_type` varchar(100) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siteconfig`
--

CREATE TABLE `siteconfig` (
  `site_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `brand` text NOT NULL,
  `logo` longtext NOT NULL,
  `favicon` text DEFAULT NULL,
  `support` longtext NOT NULL,
  `whatsapp_link` varchar(255) NOT NULL,
  `notice` longtext NOT NULL,
  `gateway` longtext NOT NULL,
  `smsapi_url` varchar(255) NOT NULL,
  `cron_token` varchar(255) DEFAULT NULL,
  `protocol` enum('http://','https://') NOT NULL,
  `baseurl` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siteconfig`
--

INSERT INTO `siteconfig` (`site_id`, `title`, `brand`, `logo`, `favicon`, `support`, `whatsapp_link`, `notice`, `gateway`, `smsapi_url`, `cron_token`, `protocol`, `baseurl`) VALUES
(1, 'Imb Pay', 'Imb Pay', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABCwAAAEiCAYAAAAyIENHAAAAAXNSR0IArs4c6QAAIABJREFUeF7snQl8FOX9/7/P7OyROyQhXIGAEASCCHKDICgIAqJFQOrPuwpaxZPWetVYrBYr2ipahXrbegAWsCqgFjnlPgUkyBEChIQk5NzsMTPP//+d3YmTZZPsnU34Pr5iwu7Mc7yfZ2fn+cz3YECFCBABIkAEiAARIAJEgAgQASJABIgAESACUUaARVl/qDtEgAgQASJABIgAESACRIAIEAEiQASIABEAEixoERABIkAEiAARIAJEgAgQASJABIgAESACUUeABIuomxLqEBEgAkSACBABIkAEiAARIAJEgAgQASJAggWtASJABIgAESACRIAIEAEiQASIABEgAkQg6giQYBF1U0IdIgJEgAgQASJABIgAESACRIAIEAEiQARIsKA1QASIABEgAkSACBABIkAEiAARIAJEgAhEHQESLKJuSqhDRIAIEAEiQASIABEgAkSACBABIkAEiAAJFrQGiAARIAJEgAgQASJABIgAESACRIAIEIGoI0CCRdRNCXWICBABIkAEiAARIAJEgAgQASJABIgAESDBgtYAESACRIAIEAEiQASIABEgAkSACBABIhB1BEiwiLopoQ4RASJABIgAESACRIAIEAEiQASIABEgAiRY0BogAkSACBABIkAEiAARIAJEgAgQASJABKKOAAkWUTcl1CEiQASIABEgAkSACBABIkAEiAARIAJEgAQLWgNEgAgQASJABIgAESACRIAIEAEiQASIQNQRIMEi6qaEOkQEiAARIAJEgAgQASJABIgAESACRIAIkGBBa4AIEAEiQASIABEgAkSACBABIkAEiAARiDoCJFhE3ZRQh4gAESACRIAIEAEiQASIABEgAkSACBABEixoDRABIkAEiAARIAJEgAgQASJABIgAESACUUeABIuomxLqEBEgAkSACBABIkAEiAARIAJEgAgQASJAggWtASJABIgAESACRIAIEAEiQASIABEgAkQg6giQYBF1U0IdIgJEgAgQASJABIgAESACRIAIEAEiQARIsKA1QASIABEgAkSACBABIkAEiAARIAJEgAhEHQESLKJuSqhDRIAIEAEiQASIABEgAkSACBABIkAEiAAJFrQGiAARIAJEgAgQASJABIgAESACRIAI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x1nQTekItm1964pkHDNmTJOFCxcy8Ub1Y+rUqXkzZsxoomLBlaJHQ0trWhuD+fPnm95+++2PN23aJGUAkjy6/Hmk1ec7058woayevD+64+PjX508efKTWVlZ521vUl54uETs1Gtt6bIil9DaT0YozZe7IVjU1lPr/jmLY7FhUCyFy2uh7paG/0oIFl7mzmYW3n7ymkYZHMeVhL8VLqw71rtgwXCerqh4euI3a6f/eCKPkqKjnKLoXaVU/UUueAQqdbhocPOm9ODFXWlgi9QLqzV1Upu1R3NpzuZsWvVbHjWKYmlLVWvKKoMa5pBzpsJBg9Oblrxz7WXdGkdHN2h32pqaTxIuli9f/lJ+fj7Le19ToE1WlDw6upY9Q+lRwe4rX71nnx/q37//36655pof9RJUMxQgbGK4fv36id99991M2b5mqW+Gk30g1VB6gUixFtik0pqamvrJzTfffLfehAp5xbKysqzvvPPOoePHjyfJ0l5KnANhEOo5/iZYUplCx44dP967dy8LlBu+/HM11KhLly7PZmdn/9N3Cnv+pG0slav5vs/UejErRQt7p06d5ubk5NToVSGvwqWXXvoye6ZksTUCte08YWPw4MGmVatWSQJGqG1f5fp//etfI//xj38w7xk1vCsq+xD74e2330655557qgQjVdX4eiqMvS/XrVs39vvvv59NRJLozqypjaEaglogtVbG1pCEFeW1eampqe/ceOONs994440at4BIF7Lv7SE/lY/88bTrc9+7K1ghLhD7azwHgkXICGssgG27YNk1tIoFoa312pUOwYKIZQb5oJd12A2pxhUNcZFOu95Rt5J1IVgw0/cXFt9+77fr/rP7TJF3kqvVwbaIlDld1CQ6iu7s1oHu7d6R4qIRuFUN3g6Xm17bkk0f7dxPp+0OijGb1Nr+IR/YVQ5kS+0uZ2Zywpn5V182pGNyXI4adWgIZWRlZVl++OGH2WvWrGEDfDYAYsHA/E1MaxsQalnd8uTk5H1Dhw4d8sknnwQ0uNPSGK3KHjVq1PVLly792Ol0svSSctdnJgyEfXBaSz299sXHx3960UUXPfnDDz9omb1CdeQPPfRQwgcffLD77NmzLIJyuPf1yWM+eJ+rQYMGXbdmzZplqldUhQLZJHH27Nl5Nputsa84rbaCyD1N7CkpKWuvu+66ke+//35QMVBeeOGF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BAIgAAIgAAIgAAIgAAIgAAIgAAIRTwCCRcR3AQAAARAAARAAARAAARAAARAAARAAAf0RgGChvzaBRSAAAiAAAiAAAiAAAiAAAiAAAiAQ8QQgWER8FwAAEAABEAABEAABEAABEAABEAABENAfAQgW+msTWAQCIAACIAACIAACIAACIAACIAACEU8AgkXEdwEAAAEQAAEQAAEQAAEQAAEQAAEQAAH9EYBgob82gUUgAAIgAAIgAAIgAAIgAAIgAAIgEPEEIFhEfBcAABAAARAAARAAARAAARAAARAAARDQHwEIFvprE1gEAiAAAiAAAiAAAiAAAiAAAiAAAhFPAIJFxHcBAAABEAABEAABEAABEAABEAABEAAB/RGAYKG/NoFFIAACIAACIAACIAACIAACIAACIBDxBCBYRHwXAAAQAAEQAAEQAAEQAAEQAAEQAAEQ0B8BCBb6axNYBAIgAAIgAAIgAAIgAAIgAAIgAAIRTwCCRcR3AQAAARAAARAAARAAARAAARAAARAAAf0RgGChvzaBRSAAAiAAAiAAAiAAAiAAAiAAAiAQ8QQgWER8FwAAEAABEAABEAABEAABEAABEAABENAfAQgW+msTWAQCIAACIAACIAACIAACIAACIAACEU8AgkXEdwEAAAEQAAEQAAEQAAEQAAEQAAEQAAH9EYBgob82gUUgAAIgAAIgAAIgAAIgAAIgAAIgEPEEIFhEfBcAABAAARAAARAAARAAARAAARAAARDQHwEIFvprE1gEAiAAAiAAAiAAAiAAAiAAAiAAAhFPAIJFxHcBAAABEAABEAABEAABEAABEAABEAAB/RGAYKG/NoFFIAACIAACIAACIAACIAACIAACIBDxBCBYRHwXAAAQAAEQAAEQAAEQAAEQAAEQAAEQ0B8BCBb6axNYBAIgAAIgAAIgAAIgAAIgAAIgAAIRTwCCRcR3AQAAARAAARAAARAAARAAARAAARAAAf0RgGChvzaBRSAAAiAAAiAAAiAAAiAAAiAAAiAQ8QQgWER8FwAAEAABEAABEAABEAABEAABEAABENAfAQgW+msTWAQCIAACIAACIAACIAACIAACIAACEU8AgkXEdwEAAAEQAAEQAAEQAAEQAAEQAAEQAAH9EYBgob82gUUgAAIgAAIgAAIgAAIgAAIgAAIgEPEEIFhEfBcAABAAARAAARAAARAAARAAARAAARDQHwEIFvprE1gEAiAAAiAAAiAAAiAAAiAAAiAAAhFPAIJFxHcBAAABEAABEAABEAABEAABEAABEAAB/RGAYKG/NoFFIAACIAACIAACIAACIAACIAACIBDxBCBYRHwXAAAQAAEQAAEQAAEQAAEQAAEQAAEQ0B8BCBb6axNYBAIgAAIgAAIgAAIgAAIgAAIgAAIRTwCCRcR3AQAAARAAARAAARAAARAAARAAARAAAf0RgGChvzaBRSAAAiAAAiAAAiAAAiAAAiAAAiAQ8QQgWER8FwAAEAABEAABEAABEAABEAABEAABENAfgf8Hs+Imh8oUALEAAAAASUVORK5CYII=', 'https://qph.cf2.quoracdn.net/main-qimg-0a3e8bf1d53417f55ad8a3cd1bf2b9d4', '+919876543210', 'https://wa.me/+919876543210', 'Please Subscribe Imb Payment Youtube Channel', '{\"paytm\":{\"mid\":\"QiCKmj32651737287077\",\"key\":\"#\"},\"upiapi\":{\"token\":\"7493ce-d99950-3cfb72-4a82a6-5c1170\",\"secret\":\"0682b9-5ac595-c47ca5-2a048e-67b5c9\"}}', 'https://api.whatsmsg.in/send-message?api_key=292h04NDOmvDTvUabZrLucOroP0eQL&sender=919157455623&number=91{NUMBER}&message={MSG}', '0682b9-5ac595-c47ca5-2a048e-67b5c9', 'https://', 'developer.imb.org.in');

-- --------------------------------------------------------

--
-- Table structure for table `store_id`
--

CREATE TABLE `store_id` (
  `sl` int(11) NOT NULL,
  `user_token` longtext NOT NULL,
  `unitId` longtext NOT NULL,
  `roleName` longtext NOT NULL,
  `groupValue` longtext NOT NULL,
  `groupId` longtext NOT NULL,
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `duration_text` varchar(100) NOT NULL DEFAULT 'Per 28 Days',
  `gst_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 for On, 0 for Off',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 for Show, 0 for Hide',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `plan_id`, `name`, `price`, `duration_days`, `duration_text`, `gst_enabled`, `is_active`, `created_at`) VALUES
(1, 1, 'Trial', 1.00, 1, '1', 1, 1, '2025-12-09 16:33:21'),
(2, 2, 'Standard', 1999.00, 28, 'Per 28 Days', 1, 1, '2025-12-09 16:33:21'),
(3, 3, 'Business', 2499.00, 28, 'Per 28 Days', 1, 1, '2025-12-09 16:33:21'),
(4, 4, 'Premium', 4999.00, 28, 'Per 28 Days', 1, 1, '2025-12-09 16:33:21'),
(5, 5, 'Enterprise Starter', 3899.00, 84, 'Per 84 Days', 1, 1, '2025-12-09 16:33:21'),
(6, 6, 'Enterprise Pro', 5999.00, 84, 'Per 84 Days', 1, 1, '2025-12-09 16:33:21'),
(7, 7, 'Enterprise Premium', 7499.00, 84, 'Per 84 Days', 1, 1, '2025-12-09 16:33:21'),
(8, 8, 'Enterprise Ultimate', 1.00, 84, 'Per 84 Days', 1, 1, '2025-12-09 16:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_report`
--

CREATE TABLE `subscription_report` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `order_id` varchar(60) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `role` enum('User','Admin','') NOT NULL DEFAULT 'User',
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sponser_id` varchar(50) DEFAULT NULL,
  `wallet` double NOT NULL DEFAULT 0,
  `withdraw_wallet` double NOT NULL DEFAULT 0,
  `hold_wallet` double NOT NULL DEFAULT 0,
  `unclear_wallet` double NOT NULL DEFAULT 0,
  `company` varchar(255) NOT NULL,
  `logo` varchar(150) DEFAULT NULL,
  `color_theme` varchar(30) DEFAULT NULL,
  `pg_qrcode` int(11) NOT NULL DEFAULT 1,
  `pg_upiapps` int(11) NOT NULL DEFAULT 1,
  `pg_upiidreq` int(11) NOT NULL DEFAULT 1,
  `pg_intent1` tinyint(4) NOT NULL DEFAULT 1,
  `pg_intent2` tinyint(4) NOT NULL DEFAULT 1,
  `pg_pby` int(11) NOT NULL DEFAULT 1,
  `pg_ads` int(11) NOT NULL DEFAULT 1,
  `two_factor` int(11) NOT NULL DEFAULT 1,
  `pin` varchar(255) NOT NULL,
  `pan` varchar(255) NOT NULL,
  `aadhaar` varchar(255) DEFAULT NULL,
  `kyc_status` tinyint(4) NOT NULL DEFAULT 0,
  `location` varchar(255) NOT NULL,
  `user_token` varchar(255) NOT NULL,
  `plan_id` tinyint(4) DEFAULT 6,
  `expiry` date NOT NULL,
  `sponser_by` varchar(50) DEFAULT NULL,
  `refer_type` int(11) DEFAULT NULL,
  `callback_url` longtext DEFAULT NULL,
  `bptoken` longtext DEFAULT NULL,
  `upiid` longtext DEFAULT NULL,
  `pg_mode` varchar(10) NOT NULL DEFAULT '1',
  `acc_lock` int(11) NOT NULL DEFAULT 0,
  `acc_ban` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `upi_id` mediumtext DEFAULT NULL COMMENT 'This is the UPI ID for PhonePe',
  `phonepe_connected` varchar(3) DEFAULT 'No',
  `hdfc_connected` varchar(3) DEFAULT 'No',
  `paytm_connected` varchar(3) DEFAULT 'No',
  `freecharge_connected` varchar(4) NOT NULL DEFAULT 'No',
  `mobikwik_connected` enum('Yes','No') NOT NULL DEFAULT 'No',
  `bharatpe_connected` varchar(3) DEFAULT 'No',
  `googlepay_connected` varchar(3) DEFAULT 'No',
  `quintuspay_connected` varchar(10) NOT NULL DEFAULT 'No',
  `amazonpay_connected` varchar(3) DEFAULT 'No',
  `sbi_connected` varchar(10) NOT NULL DEFAULT 'No',
  `route` int(11) NOT NULL DEFAULT 0,
  `instance_id` varchar(255) DEFAULT NULL,
  `instance_secret` varchar(255) DEFAULT NULL,
  `pguser_kyc` int(11) NOT NULL DEFAULT 0,
  `kyc_refid` varchar(60) DEFAULT NULL,
  `aadhar_kyc` varchar(10) NOT NULL DEFAULT '0',
  `kyc_response` text DEFAULT NULL,
  `term_and_condition` varchar(10) NOT NULL DEFAULT '0',
  `withdraw_setting` int(10) NOT NULL DEFAULT 0,
  `notif_seen` varchar(100) DEFAULT '0',
  `otp` varchar(10) DEFAULT NULL,
  `otp_attempts` int(11) NOT NULL DEFAULT 3,
  `blocked_until` timestamp NULL DEFAULT NULL,
  `primary_merchant_type` varchar(50) DEFAULT NULL,
  `primary_merchant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `mobile`, `role`, `password`, `email`, `sponser_id`, `wallet`, `withdraw_wallet`, `hold_wallet`, `unclear_wallet`, `company`, `logo`, `color_theme`, `pg_qrcode`, `pg_upiapps`, `pg_upiidreq`, `pg_intent1`, `pg_intent2`, `pg_pby`, `pg_ads`, `two_factor`, `pin`, `pan`, `aadhaar`, `kyc_status`, `location`, `user_token`, `plan_id`, `expiry`, `sponser_by`, `refer_type`, `callback_url`, `bptoken`, `upiid`, `pg_mode`, `acc_lock`, `acc_ban`, `upi_id`, `phonepe_connected`, `hdfc_connected`, `paytm_connected`, `freecharge_connected`, `mobikwik_connected`, `bharatpe_connected`, `googlepay_connected`, `quintuspay_connected`, `amazonpay_connected`, `sbi_connected`, `route`, `instance_id`, `instance_secret`, `pguser_kyc`, `kyc_refid`, `aadhar_kyc`, `kyc_response`, `term_and_condition`, `withdraw_setting`, `notif_seen`, `otp`, `otp_attempts`, `blocked_until`, `primary_merchant_type`, `primary_merchant_id`) VALUES
(1233, 'Super Admin', '12345', 'Admin', '$2y$10$t5DsAamCzuSKZ61rk6i60OYz/iBmNZo9g7n7QBvmzHlm5RDbnthl2', 'admin@gmail.com', 'PAYINDIARFL001233', 0, 0, 0, 0, 'INDIA', 'assets/company_logo/logo_692ed909de6520.64975412.png', '#1d86f7', 1, 1, 1, 1, 1, 1, 1, 0, '', '', '', 0, 'INDIA', 'e3548288ad82a58d1f25d24edb5f786f', 6, '2033-12-14', '', NULL, '', NULL, NULL, '1', 0, 'off', '', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 0, 'ITFI131pLc520434462', NULL, 0, NULL, '0', NULL, '1', 0, '0', '102917', 3, '0000-00-00 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_bank`
--

CREATE TABLE `user_bank` (
  `id` int(11) NOT NULL,
  `userid` varchar(200) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `acc_holder_name` varchar(100) DEFAULT NULL,
  `acc_no` varchar(100) DEFAULT NULL,
  `ifsc` varchar(50) DEFAULT NULL,
  `branch` varchar(80) DEFAULT NULL,
  `acc_type` tinyint(4) NOT NULL DEFAULT 1,
  `status` tinyint(4) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT 'Dragons Pay - Instant UPI Payments Solution',
  `logo` varchar(255) DEFAULT 'newassets/images/Logo.png',
  `favicon` varchar(255) DEFAULT 'newassets/images/favicon.png',
  `contact_email` varchar(255) DEFAULT 'info@pay.garudhub.in',
  `contact_phone` varchar(20) DEFAULT '+91 6200218694',
  `contact_address` text DEFAULT 'Delhi, India',
  `recaptcha_site_key` varchar(255) DEFAULT '6LcAbAgsAAAAAJ7cvR1PpoyHlChULeDsxaTZFtd-',
  `recaptcha_secret_key` varchar(255) DEFAULT '6LcAbAgsAAAAAB-2p85-_YLSWSQWAuhHiUGkb7dQ',
  `smtp_host` varchar(255) DEFAULT 'smtp.hostinger.com',
  `smtp_username` varchar(255) DEFAULT 'support@garudhub.in',
  `smtp_password` varchar(255) DEFAULT 'Raushan7x@@@',
  `smtp_port` int(11) DEFAULT 587,
  `smtp_encryption` varchar(10) DEFAULT 'tls',
  `smtp_from_email` varchar(255) DEFAULT 'support@garudhub.in',
  `smtp_from_name` varchar(255) DEFAULT 'Greenpay'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `title`, `logo`, `favicon`, `contact_email`, `contact_phone`, `contact_address`, `recaptcha_site_key`, `recaptcha_secret_key`, `smtp_host`, `smtp_username`, `smtp_password`, `smtp_port`, `smtp_encryption`, `smtp_from_email`, `smtp_from_name`) VALUES
(1, 'GARUDHUB', 'newassets/images/logo_1767806963.png', 'newassets/images/favicon_1767806912.png', 'garudhub@gmail.com', '+91 6200218694', 'Delhi, India', '6LeJaUssAAAAAEM2gJG7j1CTEkhdiC2njAxbBblq', '6LeJaUssAAAAAKWowaKZ2gVkDrQUTGdc52OW4zCo', '', '', '', 0, 'tls', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amazon_pay`
--
ALTER TABLE `amazon_pay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bharatpe_tokens`
--
ALTER TABLE `bharatpe_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `callback_report`
--
ALTER TABLE `callback_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `callback_response`
--
ALTER TABLE `callback_response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashfree_details`
--
ALTER TABLE `cashfree_details`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `freecharge`
--
ALTER TABLE `freecharge`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gpay_tokens`
--
ALTER TABLE `gpay_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hdfc`
--
ALTER TABLE `hdfc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchant`
--
ALTER TABLE `merchant`
  ADD PRIMARY KEY (`merchant_id`);

--
-- Indexes for table `merchant_domains`
--
ALTER TABLE `merchant_domains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchant_ips`
--
ALTER TABLE `merchant_ips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchant_webhooks`
--
ALTER TABLE `merchant_webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mobikwik_token`
--
ALTER TABLE `mobikwik_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_links`
--
ALTER TABLE `payment_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paytm_tokens`
--
ALTER TABLE `paytm_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phonepe_tokens`
--
ALTER TABLE `phonepe_tokens`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `plugins_list`
--
ALTER TABLE `plugins_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `popup_alert`
--
ALTER TABLE `popup_alert`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quintus_tokens`
--
ALTER TABLE `quintus_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refer_history`
--
ALTER TABLE `refer_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reported_txn`
--
ALTER TABLE `reported_txn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchantTransactionId` (`merchantTransactionId`);

--
-- Indexes for table `settlement`
--
ALTER TABLE `settlement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siteconfig`
--
ALTER TABLE `siteconfig`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `store_id`
--
ALTER TABLE `store_id`
  ADD PRIMARY KEY (`sl`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_report`
--
ALTER TABLE `subscription_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amazon_pay`
--
ALTER TABLE `amazon_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bharatpe_tokens`
--
ALTER TABLE `bharatpe_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `callback_report`
--
ALTER TABLE `callback_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `callback_response`
--
ALTER TABLE `callback_response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashfree_details`
--
ALTER TABLE `cashfree_details`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `freecharge`
--
ALTER TABLE `freecharge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `gpay_tokens`
--
ALTER TABLE `gpay_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `hdfc`
--
ALTER TABLE `hdfc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `merchant`
--
ALTER TABLE `merchant`
  MODIFY `merchant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `merchant_domains`
--
ALTER TABLE `merchant_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merchant_ips`
--
ALTER TABLE `merchant_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `merchant_webhooks`
--
ALTER TABLE `merchant_webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mobikwik_token`
--
ALTER TABLE `mobikwik_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `payment_links`
--
ALTER TABLE `payment_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `paytm_tokens`
--
ALTER TABLE `paytm_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `phonepe_tokens`
--
ALTER TABLE `phonepe_tokens`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `plugins_list`
--
ALTER TABLE `plugins_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `popup_alert`
--
ALTER TABLE `popup_alert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quintus_tokens`
--
ALTER TABLE `quintus_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `refer_history`
--
ALTER TABLE `refer_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reported_txn`
--
ALTER TABLE `reported_txn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `settlement`
--
ALTER TABLE `settlement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siteconfig`
--
ALTER TABLE `siteconfig`
  MODIFY `site_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_id`
--
ALTER TABLE `store_id`
  MODIFY `sl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subscription_report`
--
ALTER TABLE `subscription_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1250;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
