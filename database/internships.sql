-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 07:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `internships`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `companyid` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `companyname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`companyid`, `companyname`, `email`, `address`) VALUES
('0105473000036', 'Yip In Tsoi & Co., Ltd.', 'info@yipintsoi.com', '523 ถนนมหาพฤฒาราม กรุงเทพฯ'),
('0105495001213', 'IBM Thailand Co., Ltd.', '022730041', 'ถนนพหลโยธิน กรุงเทพฯ'),
('0105529045455', 'Datapro Computer Systems Co., Ltd.', '027145995', 'ถนนศรีนครินทร์ กรุงเทพฯ'),
('0105532053773', 'NTT Solutions (Thailand) Limited', '022367227', 'รัชดาภิเษก กรุงเทพฯ'),
('0105533030645', 'Professional Computer Co., Ltd.', 'helpdesk@pccth.com', 'คลองเตย กรุงเทพฯ'),
('0105534078773', 'CDG Group', 'corporate.brandingandcommunications@cdg.co.th', '202 อาคารซีดีจี เฮาส์ กรุงเทพฯ'),
('0105538124621', 'Dataone Asia (Thailand) Co., Ltd.', 'dataoneinfo@d1asia.co.th', 'พระราม 3 กรุงเทพฯ'),
('0105545020976', 'Onelink Technology Co.,Ltd.', 'cs@onelink.co.th', 'ถนนประดิษฐ์มนูธรรม กรุงเทพฯ'),
('0107546000156', 'MFEC Public Co., Ltd.', '028217999', '349 อาคารเอสเจ อินฟินิท วัน บิสซิเนส คอมเพล็กซ์ กรุงเทพฯ'),
('0107565000549', 'G-ABLE Co., Ltd.', 'contactcenter@g-able.com', 'ถนนนนทรี กรุงเทพฯ');

-- --------------------------------------------------------

--
-- Table structure for table `internship_request`
--

CREATE TABLE `internship_request` (
  `requestid` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `stdid` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `companyid` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company_position` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `coordinator_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company_tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `duration` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `internship_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `student_year` tinyint(4) DEFAULT NULL,
  `student_major` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tchID` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internship_request`
--

INSERT INTO `internship_request` (`requestid`, `stdid`, `companyid`, `company_position`, `company_name`, `coordinator_name`, `company_tel`, `duration`, `start_date`, `end_date`, `internship_type`, `student_year`, `student_major`, `status`, `tchID`) VALUES
('', '67101010367', NULL, 'นักจดหมายเหตุ', NULL, 'คุณศิริ ดีงาม', '02-700-7007', NULL, '2026-05-20', '2026-09-18', 'ฝึกงานรายวิชา', 3, 'หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา', 'เสร็จสิ้น', NULL),
('1937465028', '67101010588', '0105545020976', 'นักพัฒนาแอปพลิเคชัน', NULL, 'คุณกมล สุขใจ', '02-600-6006', '3 เดือน', '2026-05-01', '2026-07-31', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'อนุมัติ', '125'),
('2847591036', '67101010402', '0105533030645', 'ผู้ช่วย IT Support', NULL, 'คุณมานะ ใฝ่รู้', '02-800-8008', '5 เดือน', '2026-05-01', '2026-09-30', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'กำลังดำเนินการ', '127'),
('3581886032', '67101010634', NULL, 'คนผลิตชีส', 'ทอมแอนด์เจอรรี่', 'คุณนายเจ้าบ้าน', '055-555-1212', NULL, '2026-04-30', '2026-05-22', 'ฝึกงานรายวิชา', 2, 'หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา', 'กำลังดำเนินการ', NULL),
('3629184750', '67101010487', '0105473000036', 'ผู้ช่วยบรรณารักษ์', NULL, 'คุณนภา สวัสดี', '02-300-3003', '4 เดือน', '2026-06-01', '2026-09-30', 'ฝึกงานรายวิชา', 3, 'สารสนเทศศึกษา', 'ปฏิเสธ', '124'),
('4759201836', '67101010398', '0105534078773', 'นักจัดการข้อมูล', NULL, 'คุณพรรณี ตั้งใจ', '02-200-2002', '4 เดือน', '2026-05-01', '2026-08-31', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'อนุมัติ', '124'),
('5488060367', '67101010639', NULL, 'คนผลิตชีส', 'ทอมแอนด์เจอรรี่', 'คุณหญิง', '055-555-1212', NULL, '2026-04-30', '2027-04-17', 'ฝึกตามความต้องการนอกหลักสูตร', 2, 'หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา', 'กำลังดำเนินการ', NULL),
('5618293740', '67101010355', '0105532053773', 'นักพัฒนาระบบ', NULL, 'คุณชาญ ฉลาดดี', '02-236-7227', '6 เดือน', '2026-05-15', '2026-11-15', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'กำลังดำเนินการ', '129'),
('5839201746', '67101010312', '0107546000156', 'นักพัฒนาซอฟต์แวร์', NULL, 'คุณสมชาย ใจดี', '02-821-7999', '4 เดือน', '2026-05-01', '2026-08-31', 'ฝึกงานรายวิชา', 4, 'สารสนเทศศึกษา', 'กำลังดำเนินการ', '123'),
('6502918473', '67101010367', '0105538124621', 'นักจดหมายเหตุ', NULL, 'คุณศิริ ดีงาม', '02-700-7007', '4 เดือน', '2026-06-01', '2026-09-30', 'ฝึกงานรายวิชา', 3, 'สารสนเทศศึกษา', 'ปฏิเสธ', '126'),
('7392018456', '67101010574', '0105495001213', 'นักวิเคราะห์ข้อมูล', NULL, 'คุณลัดดา สดใส', '02-273-0041', '3 เดือน', '2026-05-01', '2026-07-31', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'อนุมัติ', '128'),
('7491026358', '67101010312', '0105534078773', 'นักวิเคราะห์ระบบ', NULL, 'คุณวิภา รักงาน', '02-200-2002', '3 เดือน', '2026-05-01', '2026-07-31', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'อนุมัติ', '123'),
('8201746395', '67101010445', '0107565000549', 'ผู้ช่วยวิศวกรระบบ', NULL, 'คุณอนันต์ ขยันดี', '02-500-5005', '2 เดือน', '2026-06-01', '2026-07-31', 'ฝึกงานรายวิชา', 3, 'สารสนเทศศึกษา', 'กำลังดำเนินการ', '126'),
('9182746503', '67101010521', '0105529045455', 'นักพัฒนาเว็บ', NULL, 'คุณธีระ มั่นใจ', '02-714-5995', '6 เดือน', '2026-05-15', '2026-11-15', 'สหกิจศึกษา', 4, 'สารสนเทศศึกษา', 'อนุมัติ', '125'),
('9252137968', '67101010634', NULL, 'ตลก', 'เชิญยิ้ม', 'เชิญยิ้ม', '555-555-5555', NULL, '2026-04-29', '2026-07-17', 'ฝึกงานรายวิชา', 2, 'หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา', 'ปฏิเสธ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffid` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `staffname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tchID` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) DEFAULT '1234'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffid`, `staffname`, `email`, `phone`, `tchID`, `password`) VALUES
('STF001', 'อนุชา แสงทอง', 'anucha.staff@gmail.com', '0819257346', '123', '1234'),
('STF002', 'ศิริพร พูนสุข', 'siriporn.staff@gmail.com', '0956824137', '124', '1234'),
('STF003', 'วิทยา จันทร์ดี', 'witthaya.staff@gmail.com', '0893175624', '124', '1234'),
('STF004', 'กัญญา วงศ์ดี', 'kanya.staff@gmail.com', '0937461825', '124', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `status_log`
--

CREATE TABLE `status_log` (
  `logid` int(11) NOT NULL,
  `requestid` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `stdid` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `staffID` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_log`
--

INSERT INTO `status_log` (`logid`, `requestid`, `stdid`, `status`, `staffID`) VALUES
(1, '5839201746', '67101010312', 'กำลังดำเนินการ', NULL),
(2, '7491026358', '67101010312', 'อนุมัติ', NULL),
(3, '3629184750', '67101010487', 'ปฏิเสธ', NULL),
(4, '9182746503', '67101010521', 'กำลังดำเนินการ', NULL),
(5, '4759201836', '67101010398', 'อนุมัติ', NULL),
(6, '8201746395', '67101010445', 'กำลังดำเนินการ', NULL),
(7, '1937465028', '67101010588', 'อนุมัติ', NULL),
(8, '6502918473', '67101010367', 'ปฏิเสธ', NULL),
(9, '2847591036', '67101010402', 'กำลังดำเนินการ', NULL),
(10, '7392018456', '67101010574', 'อนุมัติ', NULL),
(11, '5618293740', '67101010355', 'กำลังดำเนินการ', NULL),
(12, '', '67101010367', 'ออกใบส่งตัว', 'STF002'),
(13, '', '67101010367', 'เสร็จสิ้น', 'STF002'),
(14, '5839201746', '67101010312', 'กำลังดำเนินการ', 'STF001'),
(15, '9182746503', '67101010521', 'ออกใบส่งตัว', 'STF002'),
(16, '9182746503', '67101010521', 'ออกใบส่งตัว', 'STF001'),
(17, '9182746503', '67101010521', 'กำลังดำเนินการ', 'STF001');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `stdid` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `stdname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) DEFAULT '1234'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`stdid`, `stdname`, `email`, `phone`, `password`) VALUES
('67101010312', 'กิตติพงศ์ ศรีสุข', 'kittipong.s@g.swu.ac.th', '0865291743', '1234'),
('67101010355', 'อิทธิพล แก้วทอง', 'ittipol.k@g.swu.ac.th', '0962718453', '1234'),
('67101010367', 'วราภรณ์ ศรีคำ', 'waraporn.s@g.swu.ac.th', '0836492751', '1234'),
('67101010398', 'ณัฐธิดา บุญมี', 'nattida.b@g.swu.ac.th', '0918473620', '1234'),
('67101010402', 'นพดล ศรีวิชัย', 'nopadon.s@g.swu.ac.th', '0873129645', '1234'),
('67101010445', 'ปวีณา ทองดี', 'paveena.t@g.swu.ac.th', '0895627138', '1234'),
('67101010487', 'พิมพ์ชนก วัฒนะ', 'pimchanok.w@g.swu.ac.th', '0941836205', '1234'),
('67101010521', 'ธนกฤต จันทรา', 'thanakrit.c@g.swu.ac.th', '0827619534', '1234'),
('67101010574', 'ชลธิชา ใจดี', 'chonticha.j@g.swu.ac.th', '0928451736', '1234'),
('67101010588', 'ศุภชัย อินทร์แก้ว', 'supachai.i@g.swu.ac.th', '0951738246', '1234'),
('67101010626', 'ธนัญญา กรรพฤทธิ์', 'tnyeiei@g.swu.ac.th', '0863193124', '1234'),
('67101010634', 'พรนภัส เจียมโฆสิต', 'pornnapat.row@g.swu.ac.th', '0881410917', '$2y$10$HC0.oJ72CEDnVVxYjzzFO.lxMu87EZmXruiOVZCe0NFiwwh898Phu'),
('67101010639', 'แพรวทอง พรมมา', 'paewthong.phormma@g.swu.ac.th', '01234567890', '$2y$10$lSVhxyT31/SxwCqhvpql7OJerRH3BDcEG0buux5woN9GizAQcFD7S');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `tchID` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `tchname` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) DEFAULT '1234'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`tchID`, `tchname`, `email`, `phone`, `password`) VALUES
('123', 'อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์', 'dit@g.swu.ac.th', '0815550581', '1234'),
('124', 'อาจารย์ ดร. ฐิติ อติชาติชยากร', 'thitik@g.swu.ac.th', '026495000', '1234'),
('125', 'ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์', 'vipakorn@g.swu.ac.th', '026495000', '1234'),
('126', 'อาจารย์ ดร. โชคธำรงค์ จงจอหอ', 'chokthamrong@g.swu.ac.th', '026495000', '1234'),
('127', 'อาจารย์โชติมา วัฒนะ', 'chotimaw@g.swu.ac.th', NULL, '1234'),
('128', 'ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ', 'dussadee@g.swu.ac.th', '026495000', '1234'),
('129', 'ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร', 'sasipimol@g.swu.ac.th', NULL, '1234'),
('130', 'อาจารย์ ดร. ศุมรรษตรา แสนวา', 'sumattra@g.swu.ac.th', '0856179617', '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`companyid`);

--
-- Indexes for table `internship_request`
--
ALTER TABLE `internship_request`
  ADD PRIMARY KEY (`requestid`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffid`);

--
-- Indexes for table `status_log`
--
ALTER TABLE `status_log`
  ADD PRIMARY KEY (`logid`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`stdid`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`tchID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `status_log`
--
ALTER TABLE `status_log`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
