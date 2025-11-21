-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 06:06 AM
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
-- Database: `fsktmpsm`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `email`, `password`) VALUES
('admin', 'admin@uthm.edu.my', '$2y$10$R45/Ux0cnlHLERJAdV2ZMeHi6rcQl.4W91B71hBJcvfVxvZNmD9XO');

-- --------------------------------------------------------

--
-- Table structure for table `chapter`
--

CREATE TABLE `chapter` (
  `id` int(11) NOT NULL,
  `id_pelajar` int(11) NOT NULL,
  `chapter` varchar(50) NOT NULL,
  `chapter_file` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `supervisor_comment` text DEFAULT NULL,
  `file_corrected` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapter`
--

INSERT INTO `chapter` (`id`, `id_pelajar`, `chapter`, `chapter_file`, `status`, `supervisor_comment`, `file_corrected`, `submitted_at`) VALUES
(20, 2, 'LAPORAN 1', 'uploads/chapters/1763393645_Bus_Ticket_MYUCC44543026 (1).pdf', 'Menunggu Semakan', 'okey notedddd ', '1760930637_proposal_6859f39a9df0a0.48520786.pdf', '2025-11-17 23:34:05'),
(21, 3, 'Chapter 1', 'uploads/chapters/1760939595_proposal_6859f4fdec7259.46242452.pdf', 'Reviewed', 'OKEY BOLEH ', '1760939853_proposal_6859f39a9df0a0.48520786.pdf', '2025-10-20 13:53:15'),
(22, 9, 'Laporan 1', 'uploads/chapters/1760965595_proposal_6859f4fdec7259.46242452.pdf', 'Reviewed', 'tolong buat semulaa', '1760965679_proposal_6859f10971deb7.51353102.pdf', '2025-10-20 21:06:35'),
(23, 10, 'Laporan 1', 'uploads/chapters/1761042171_1748109989_proposal_aqilah_di230041_v2.pdf', 'Reviewed', 'wug9wegjp', '1761042273_1748178775_proposal_aqilah_di230041_v2.pdf', '2025-10-21 18:22:51'),
(24, 10, 'Laporan 2', 'uploads/chapters/1761042344_1760895173_8_proposal_6859f39a9df0a0.48520786.pdf', 'Menunggu Semakan', NULL, NULL, '2025-10-21 18:25:44'),
(25, 12, 'Laporan 1', 'uploads/chapters/1761570727_1761445585_att_e15_bmc_santanasahara.pdf', 'Reviewed', 'OKEY SASAYAA TERIMA', '1761570782_1761445585_att_e15_bmc_santanasahara.pdf', '2025-10-27 21:12:07'),
(26, 12, 'Laporan 2', 'uploads/chapters/1761570901_1761445187_att_1747939114_chapter1_aqilah_di230041_aftercorrection.pdf', 'Reviewed', 'BYNU7VV', '1761677211_lab1_instruction.pdf', '2025-10-27 21:15:01'),
(27, 15, 'Laporan 1', 'uploads/chapters/1761891430_6_proposal_6859f39a9df0a0.48520786.pdf', 'Menunggu Semakan', NULL, NULL, '2025-10-31 14:17:10'),
(28, 16, 'Laporan 1', 'uploads/chapters/1761897309_1747766883_Assessing-the-Impact-of-Organizational-Readiness-and-Digital-Financial-Innovation-on-Financial-Resilience-Evidence-from-Bank-ing-Sector-of-an-Emerging-Economy.pdf', 'Reviewed', 'OKEY SAYA DH LIHATT', '1761897356_1747766883_Assessing-the-Impact-of-Organizational-Readiness-and-Digital-Financial-Innovation-on-Financial-Resilience-Evidence-from-Bank-ing-Sector-of-an-Emerging-Economy.pdf', '2025-10-31 15:55:09'),
(29, 16, 'Laporan 2', 'uploads/chapters/1761905856_6_proposal_6859f39a9df0a0.48520786.pdf', 'Menunggu Semakan', NULL, NULL, '2025-10-31 18:17:37'),
(30, 2, 'LAPORAN 2', 'uploads/chapters/1763393619_Bus_Ticket_MYUCC44543026 (1).pdf', 'Menunggu Semakan', NULL, NULL, '2025-11-17 23:33:39');

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `info_type` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`id`, `title`, `content`, `link`, `attachment`, `info_type`, `category`, `image`, `created_at`) VALUES
(21, 'Additional Info', '<ul>\r\n	<li>\r\n	<p><strong>AITCS Related - YouTube Links</strong></p>\r\n\r\n	<ul>\r\n		<li>[2025] How to write &amp; Submit proceeding paper AITCS ( New Template):&nbsp;<a href=\"https://youtu.be/DLn0qqY-HhI\">https://youtu.be/DLn0qqY-HhI</a></li>\r\n		<li>Guide to Write AITCS Proceeding:&nbsp;<a href=\"https://www.youtube.com/watch?v=bF1ABEmZg6k\">https://www.youtube.com/watch?v=bF1ABEmZg6k</a>&nbsp;</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Slides &amp; Recorded Briefing🎥 for PSM 1 Semester 1 2025/2026</strong></p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>[PSM 1]&nbsp;<a href=\"https://uthmedumy.sharepoint.com/:p:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/Ea8NkdObJURItb8tTcOLyKIBR0tQQlhyN8wDwMub_pIlxQ?e=ahjuxU\" target=\"_blank\">Slides</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1umnZ8uatsWur1iYY2mROQHfxd9OczhnG/view?usp=sharing\" target=\"_blank\">Briefing video with coordinator</a>, Briefing video for&nbsp;<a href=\"https://drive.google.com/file/d/1SjLZa_wc_-HCR0vZvgL33cNB8X5w6p20/view?usp=sharing\" target=\"_blank\">BIP</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1BcUzR3DexcUpp4zsBDF1-DQ0wTKldHPS/view?usp=sharing\" target=\"_blank\">BIS</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1Nln76a_PdtPula1H9WD6OxejkaK5eSfy/view?usp=sharing\" target=\"_blank\">BIW</a>,<a href=\"https://drive.google.com/file/d/1a5arDISU8Y8Fq9mAAoBVOsyrG8GjrWxr/view?usp=sharing\" target=\"_blank\">&nbsp;BIT</a>&nbsp;(Video unavailable for BIM).</p>\r\n		</li>\r\n		<li>\r\n		<p>[PSM 1] 2nd Briefing -&nbsp;<a href=\"https://uthmedumy.sharepoint.com/:p:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EV9WavSb9LVFnqb76btu-hMBk558dh5qL4gVMhc_itS8rQ?e=IqPF2b\" target=\"_blank\">Slides</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1SDhQGb5fKuXP87ssdLDgzFyJ6wde5Eau/view?usp=sharing\" target=\"_blank\">Briefing video with coordinator</a></p>\r\n		</li>\r\n		<li>\r\n		<p>[PSM 2]&nbsp;<a href=\"https://uthmedumy.sharepoint.com/:p:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EZ0mrH7TxExFirHv7_F1LNgB7LstFPCHCxNQhhz_ci6CIg?e=cCkpih\" target=\"_blank\">Slides</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1Z_8jTKX2aY-7HX0bPnatvxAqPWYHx36K/view?usp=sharing\" target=\"_blank\">Briefing video with coordinator</a></p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>Slides &amp; Recorded Briefing🎥 for PSM 1 and PSM 2 for Semester 2 2024/2025 - 21.02.2025.</strong></p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>[PSM 1]&nbsp;<a href=\"https://uthmedumy.sharepoint.com/:p:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/ESk4XPrXUw9MqXx0yZJ8RhQBOJE6M8iObXCrDA2o_pu7bA?e=LBC1qU\" target=\"_blank\">Slides</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1ps9Q5rhp3s0jUPPcq_UDYwXm7eYQBHs5/view?usp=sharing\" target=\"_blank\">Briefing video with coordinator</a>, Briefing video for&nbsp;<a href=\"https://drive.google.com/file/d/16CI-NWHTboLPUOu1MoYosvnR6Jl7XDCB/view?usp=sharing\" target=\"_blank\">BIP</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1SbdkLucDMdOIfw1RzaJbH-LtVhgTC-AM/view?usp=sharing\" target=\"_blank\">BIS</a>,&nbsp;<a href=\"https://drive.google.com/file/d/1gla4cFFCqL5IaG7kEmXVkJZWYr90AhIm/view?usp=sharing\" target=\"_blank\">BIW</a>, BIM(Not available),&nbsp;<a href=\"https://youtu.be/bgHde1e7Jnk?si=KwMd_6YF4exM6Bva\" target=\"_blank\">BIT</a>.</p>\r\n		</li>\r\n		<li>\r\n		<p>[PSM 2]&nbsp;<a href=\"https://uthmedumy.sharepoint.com/:p:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/Eb95zD7gnjBOiLDF__nVvyoBoOQ1DRPEo2fQm6MF6TP8wA?e=u4i7UP\" target=\"_blank\">Slides</a></p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>PSM 1 &amp; PSM 2 marks distribution [</strong><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/Ee1NpBI-ffZKvB_8WqTaVeUBUCfhHouclw-I-e_sCun8AQ?e=WruyLR\" target=\"_blank\"><strong>CLICK HERE</strong></a><strong>]</strong></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>PSM Titles from Previous Semesters&nbsp;</strong></p>\r\n\r\n	<ul>\r\n		<li>\r\n		<p>BIP Programme: [<a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EQJswRKUsDNFqD73Y6J9a2gBFRCuKZbZ29HOfGCvfdwEvA?e=sKMbiz\" target=\"_blank\">click here</a>]</p>\r\n		</li>\r\n		<li>\r\n		<p>BIS Programme: [<a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EdkFN6I8ycNDvK63nUcdsJ8BRGeTF9yVOCQSsPS3Q4PZuw?e=8gyP1o\" target=\"_blank\">click here</a>]</p>\r\n		</li>\r\n		<li>\r\n		<p>BIW Programme: [<a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EZ78v6rd05RAhmcwQn6GZRwBc17kvCYJo2kP59EGTBMpYg?e=NcE04m\" target=\"_blank\">click here</a>]</p>\r\n		</li>\r\n		<li>\r\n		<p>BIM Programme: [<a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EQPBx2x1LSlDiZRNXATWensB6t7jZZAe818tPf0KzWLgUQ?e=YMIzGh\" target=\"_blank\">click here</a>]</p>\r\n		</li>\r\n		<li>\r\n		<p>BIT Programme: [<a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EYyB-aKI99FOktQhqZnJRG4B_xT0gbrgaJZhztQ22RpYRQ?e=Mh44qc\" target=\"_blank\">click here</a>]</p>\r\n		</li>\r\n	</ul>\r\n	</li>\r\n	<li>\r\n	<p><strong>List of FSKTM Academic Staff by Programme&nbsp;[</strong><a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EQWzxaMzcUtOvK1Dqh6U810Bxp3Jhc0VLRy6vIuSUTugGQ?e=IRxkBC\" target=\"_blank\"><strong>CLICK HERE</strong></a><strong>]</strong></p>\r\n	</li>\r\n	<li>\r\n	<p><strong>Turnitin Account: Students are preferably requested to ask supervisors to provide Turnitin classroom. Reports must be below 30% similarity index. (Note: Only for reports written in English ONLY)</strong></p>\r\n	</li>\r\n</ul>\r\n', '', NULL, '', NULL, '1761681233_img_1748049315_timeline.webp', '2025-10-28 19:53:07'),
(25, 'UM6UM46U', '<table align=\"center\" border=\"3\" cellpadding=\"2\" cellspacing=\"2\" style=\"width:800px\">\r\n	<thead>\r\n		<tr>\r\n			<td>\r\n			<p><strong>Form Label</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>Form Name</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>PSM1</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp;PSM2&nbsp;</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>DOWNLOAD</strong></p>\r\n\r\n			<p><strong>Forms</strong></p>\r\n\r\n			<p><strong>(right-click, open in new tab)</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>DOWNLOAD&nbsp;</strong></p>\r\n\r\n			<p><strong>ssessment Rubrics</strong></p>\r\n\r\n			<p><strong>(right-click, open in new tab)</strong></p>\r\n			</td>\r\n		</tr>\r\n	</thead>\r\n	<tbody>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran A</p>\r\n\r\n			<p><em>(Form A)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Persetujuan Penyelia PSM&nbsp;</p>\r\n\r\n			<p><em>(PSM Supervisor Agreement Form)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/Ed3R0WuVUMhPqY2IDaa0oFoBq9yuUjGNZqhz5MlYaZni_A?e=pWHRFl\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran B</p>\r\n\r\n			<p><em>(Form B)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><em>Borang Cadangan Tajuk PSM&nbsp;</em><em>- new 051025</em></p>\r\n\r\n			<p><em>(PSM Project Title Form)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EZaWsJlyzlVHtp-AmWvJvu4B1oNoJzdWXS3-0nAJCvD8VA?e=aZIGHa\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran C</p>\r\n\r\n			<p><em>(Form C)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Permohonan Menulis Laporan Akhir Project Sarjana Muda dalam Bahasa Inggeris&nbsp;<em>- new 051025</em></p>\r\n\r\n			<p><em>(Application to Write Final Year Project Report in English)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EYVpNAvfNB1KmiD7QRRqXdwBtKSCi1-wO6Qfhc-xxQuMWQ?e=GUgGPL\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/sites/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/SiteAssets/SitePages/TopicHome(1)/2856112462.gif\" style=\"height:40px; width:40px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran D</p>\r\n\r\n			<p><em>(Form D)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><em>Borang Pengeluaran Surat Amaran PSM</em></p>\r\n\r\n			<p><em>(PSM Warning Letter Form)</em></p>\r\n\r\n			<p><strong>Tindakan Penyelia Sahaja/Action by Supervisor Only</strong></p>\r\n\r\n			<p><strong>*Note: Student will get the warning letter for various circumstances e.g. failure to attend weekly meeting, etc.</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp; &nbsp;Y</strong></p>\r\n\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n\r\n			<p>&nbsp;</p>\r\n\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EUmXnkB2LZ9FoJ2tUtI8nj0B-OnoO3jdqy4nocjNQsAyUw?e=s9D1dP\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran X</p>\r\n\r\n			<p><em>(Form X)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><em>Borang Penilaian PSM 1 bagi Penyelia&nbsp;</em></p>\r\n\r\n			<p><em>(Supervisor Evaluation Form)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EWR_ek-Ys1FOjVUFwHSiQR0BUQRecF7rQBEu2-xLIv-U7g?e=gOTTme\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>(refer to rubric for Form I &amp; rubric for Form F)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran F</p>\r\n\r\n			<p><em>(Form F)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penilaian Buku Log bagi Penyelia</p>\r\n\r\n			<p><em>(Log book Assessment Form for Supervisor)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp; Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EWSiDwWb-UtIhepy11063NcBJw3Tt3_JQT7J-N9FJmi-5Q?e=dadZLA\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EX4B7ShQkIVJnBBAApFA04kBXbK8oMBWfzXLNQTln92_Qw?e=w6HX8h\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:40px; width:40px\" /></a></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran G</p>\r\n\r\n			<p><em>(Form G)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penilaian Laporan Akhir bagi Penyelia</p>\r\n\r\n			<p><em>(Final Report Assessment Form for Supervisor)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EemUSKB6VSpDj8I5ER-SssQBSzxAsSwXj_WUrT9ypo0Xdw?e=9YY7xT\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EQO71R4u8fZNhk-ARQH4hEMByZeAx-C1DneibJNnafhQlw?e=ayz3Gf\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:40px; width:40px\" /></a></p>\r\n\r\n			<p>(and refer to Form J rubric for Part B evaluation, i.e.&nbsp;<em>Hasil Projek</em>)</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran H</p>\r\n\r\n			<p><em>(Form H)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Kemajuan 1 dan 2 PSM 2</p>\r\n\r\n			<p><em>(PSM 2 Progress 1 and 2 Form)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/Eac9Tdb-97JOl1VTd_zn01QBcWXpzA_uOxv4yT9Lf7Em2w?e=7QFl5E\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/ETM8FPuMgStPtMcXmZ7i4TgBRPWRERTb_EvJPPm8V0RV2w?e=qMSDsn\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:40px; width:40px\" /></a></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran I</p>\r\n\r\n			<p><em>(Form I)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penilaian PSM 1 bagi Penilai</p>\r\n\r\n			<p><em>(PSM 1 Assessment Form - for Panel)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EYuRux20P4FKkMVv-3wNTg0Be1Rs2RxAGfvX2CJ4FO_WUQ?e=3aDodn\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:f:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EorpoqYmi-ZJnQQP_84V_bABPtJfGemdhds5Imh0B3mXFg?e=SoDfbz\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:40px; width:40px\" /></a></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran J</p>\r\n\r\n			<p><em>(Form J)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penilaian PSM 2 bagi Penilai</p>\r\n\r\n			<p><em>(PSM 2 Assessment Form - for Panel)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EUxKsapuk3tDib2Olo7jCI8B86WZ3vLEB18jlT6-TS8bVA?e=v3V4Af\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:f:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EkA-yz2LzUlBoU82QpVVWosBl5gZKFuOspBg_xmDQxMqGA?e=VsGbMI\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:40px; width:40px\" /></a></p>\r\n\r\n			<p><em>(new update - 160525)</em></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n\r\n			<p>Lampiran K</p>\r\n\r\n			<p><em>(Form K)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penyerahan Laporan Akhir</p>\r\n\r\n			<p><em>(Final Report Submission Form)</em></p>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EX5B7Ncwrm5IvaDGaiHIMtYBKd7_mVIs_WjF1lvxe7RsTQ?e=39Zipr\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran R1</p>\r\n\r\n			<p><em>(Form R1)</em></p>\r\n\r\n			<p><em>(new update - 271025)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang Penyerahan dan Semakan Bab dalam Laporan, Kertas Prosiding &amp; Prototaip Sistem/Kerangka Penyelidikan</p>\r\n\r\n			<p><em>(Report Chapter, Proceeding Paper &amp; System Prototype/Research Framework Submission and Reviewing Form)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EQOw1LALDHBIlJuNj-EnrIUBeKLR16cv6s9b5bdPPx5Z_A?e=NahBMM\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/_api/v2.1/sites/uthmedumy.sharepoint.com,a3c9b466-df3d-452e-9b5d-abbb1268361c,c9d4c514-ee4b-41ba-9b02-cdb9cee01677/lists/b66b098a-0785-4917-9ccb-60854a59d001/items/9a3722d2-d67f-48aa-9a78-66dc72a29d2c/driveItem/thumbnails/0/c400x99999/content?prefer=noRedirect,extendCacheMaxAge&amp;clientType=modernWebPart&amp;ow=35&amp;oh=35&amp;format=webp\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran R2</p>\r\n\r\n			<p><em>(Form R2)</em></p>\r\n\r\n			<p><em>(new update - 160525)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><em>Borang Pengesahan Semakan Laporan dan Kebenaran Memuat Naik ke e-Report</em></p>\r\n\r\n			<p><em>(Report Review Confirmation and Permission to Upload to eReport)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/ER5viU5bnVpCoN6aKEtoHVEBB1aid1bD0R_K-J7Urw3FAw?e=SSodu4\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/sites/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/SiteAssets/SitePages/TopicHome(1)/3577289268.gif\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n\r\n			<p>&nbsp;</p>\r\n\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Lampiran R3</p>\r\n\r\n			<p><em>(Form R3)</em></p>\r\n\r\n			<p><em>(new update - 160525)</em></p>\r\n			</td>\r\n			<td>Borang Permohonan Penggunaan Pelayan Komputer FSKTM untuk Tujuan Hosting bagi Projek Sarjana Muda<br />\r\n			<em>(Application form for the Use of FSKTM Computer Server for Undergraduate Project Hosting Purposes)</em><br />\r\n			&nbsp;</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; Y</strong></p>\r\n			</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:b:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EccGOEktOC9Mo9hVFfTY2a8BJvmrzOQ5yRiIVfay3qbpqQ?e=5sF61y\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/sites/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/SiteAssets/SitePages/TopicHome(1)/3577289268.gif\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>Borang HIRARC</p>\r\n\r\n			<p><em>(HIRARC Form)</em></p>\r\n\r\n			<p><em>(new &nbsp;221025)</em></p>\r\n			</td>\r\n			<td>\r\n			<p>Borang HIRARC UTHM bagi Pelajar PSM</p>\r\n\r\n			<p><em>(UTHM HIRARC Form for PSM Students)</em></p>\r\n			</td>\r\n			<td>\r\n			<p><strong>&nbsp; &nbsp; &nbsp;Y</strong></p>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>\r\n			<p><a href=\"https://uthmedumy.sharepoint.com/:x:/s/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/EdckqiL34-dKuF6dR-Gxi2kBU3MI47C0pmMnz-uHzb-lXQ?e=ZmjJJv\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/sites/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/SiteAssets/SitePages/TopicHome(1)/3577289268.gif\" style=\"height:39px; width:39px\" /></a></p>\r\n\r\n			<p>right click &amp; open in new tab</p>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td>\r\n			<p>-</p>\r\n			</td>\r\n			<td>\r\n			<p>*[FSKTM] Permohonan bagi memperoleh Surat Kebenaran Mendapatkan Maklumat Dari Organisasi Luar</p>\r\n\r\n			<p><em>*[FSKTM]&nbsp;Application for a Letter of Authorization to Obtain Information from an External Organization</em></p>\r\n\r\n			<p>&nbsp;</p>\r\n\r\n			<p><em><strong>INSTRUCTION</strong>:</em></p>\r\n\r\n			<p><em>1. Complete the form (on the link provided)</em></p>\r\n\r\n			<p><em>2. Check your email. You will receive an application form (in *.pdf), get approval from supervisor.</em><br />\r\n			<em>3</em>.&nbsp;<em>Contact Person in Charge(PIC), i.e. En Norwan (FSKTM) at Whatsapp number 07-4533643.&nbsp;</em></p>\r\n\r\n			<ul>\r\n				<li>\r\n				<p><em>State your name, matric number and inform that you have submitted an application form via Google Form.&nbsp;</em></p>\r\n				</li>\r\n				<li>\r\n				<p><em>Attach the pdf file (completed with supervisor&#39;s approval to PIC).</em></p>\r\n				</li>\r\n			</ul>\r\n\r\n			<p><em>PIC will send the official FSKTM Authorization Letter to you via WhatsApp within 3 working days, or once it is ready.</em></p>\r\n			</td>\r\n			<td>&nbsp;</td>\r\n			<td>&nbsp;</td>\r\n			<td>\r\n			<p><a href=\"https://docs.google.com/forms/d/e/1FAIpQLScll7cwQeITtfmkECOrMMtyO0skFXSMW4pjRBJilqNQ-31CvA/viewform?pli=1\" target=\"_self\"><img alt=\"\" src=\"https://uthmedumy.sharepoint.com/sites/FSKTMPROJEKSARJANAMUDA_PSM_StudentandStaf/SiteAssets/SitePages/TopicHome(1)/3566076272.gif\" style=\"height:41px; width:41px\" /></a></p>\r\n\r\n			<p>click the link here</p>\r\n\r\n			<p>&nbsp;</p>\r\n\r\n			<p>&nbsp;</p>\r\n\r\n			<p>&nbsp;</p>\r\n			</td>\r\n			<td>\r\n			<p>&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p>&nbsp;</p>\r\n', '', NULL, '', NULL, NULL, '2025-10-30 14:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `logbook`
--

CREATE TABLE `logbook` (
  `id` int(11) NOT NULL,
  `permohonan_id` int(11) NOT NULL,
  `week_no` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Belum Hantar',
  `log_content` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `supervisor_comment` text DEFAULT NULL,
  `supervisor_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `progress` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook`
--

INSERT INTO `logbook` (`id`, `permohonan_id`, `week_no`, `start_date`, `end_date`, `status`, `log_content`, `action_taken`, `attachment`, `supervisor_comment`, `supervisor_status`, `created_at`, `updated_at`, `progress`) VALUES
(18, 37, 1, '2025-10-20', '2025-10-25', 'Approved', '/t8/t8bibviwbnvrpivni3ro3', 'vg3rg3rb3', '[\"1763396746_Bus_Ticket_MYUCC44543026__1_.pdf\",\"1763396746_Bus_Ticket_MYUCC44543026.pdf\",\"1763396746_Bus_Ticket_MYUCC25592168.pdf\"]', ' I, 5.5,B7 Lym6u5j56j', 'Approved', '2025-10-20 03:28:37', '2025-11-17 16:25:46', 98),
(19, 37, 2, '2025-10-27', '2025-10-31', 'Pending', 'MANAAA ERMM ', '', '[\"1763396758_Bus_Ticket_MYUCC44543026__1_.pdf\",\"1763396758_Bus_Ticket_MYUCC44543026.pdf\",\"1763396758_Bus_Ticket_MYUCC25592168.pdf\"]', 'OKEY NOTED44j2g13ogj1p35bkp[5', 'Approved', '2025-10-20 03:59:48', '2025-11-17 16:25:58', 0),
(20, 37, 3, '2025-11-03', '2025-11-07', 'Approved', 'HIIII  SAYAA BUAT BANYAK BENDA MINGGUU NI ', NULL, '1760936394_proposal_6859f0bbb2e4b6.06452908.pdf', 'OKEYY NOTEDDD I AGGREEE', 'Approved', '2025-10-20 04:59:38', '2025-10-20 05:00:17', 0),
(22, 38, 1, '2025-10-20', '2025-10-25', 'Approved', 'HIII , MINGGU NI SAYA BUAT ITU INI', NULL, '1760939815_proposal_6859f4fdec7259.46242452.pdf', 'OKEY SAYA BOLEH DITERIMA', 'Approved', '2025-10-20 05:56:34', '2025-10-20 05:57:58', 0),
(23, 39, 1, '2025-10-21', '2025-10-24', 'Approved', 'hari nii buat ayam ', NULL, '1760965889_proposal_6859f10971deb7.51353102.pdf', 'okey saya setuju', 'Approved', '2025-10-20 13:09:06', '2025-10-20 13:12:06', 0),
(24, 40, 1, '2025-10-21', '2025-10-25', 'Approved', 'minggu n saya buat ayam', 'bet4tn4', '[\"1761460907_1176-1193.pdf\"]', 'okey saya seyjujut', 'Approved', '2025-10-21 10:28:21', '2025-10-26 06:41:47', 0),
(25, 37, 4, '2025-11-03', '2025-11-07', 'Belum Hantar', '23g24g4g315', 'evr3bt3b4yn24', '[\"1761458045_nuraqilahannuar_lab4-preprocessing_di230041.pdf\",\"1761458045_Activity2_Data_Preprocessing_Report.pdf\",\"1761458045_lab4-preprocessing (1).pdf\"]', NULL, 'Pending', '2025-10-26 05:21:10', '2025-10-26 05:57:23', 0),
(26, 37, 5, '2025-10-05', '2025-10-16', 'Approved', 'gvgjvuibo9', 'erbebtbn4tn', '[\"1761460528_1760932799_proposal_6859f4fdec7259.46242452.pdf\"]', '', 'Approved', '2025-10-26 06:04:20', '2025-10-26 06:37:41', 0),
(27, 37, 6, '2025-11-04', '2025-11-08', 'Approved', '', '', '[\"1761461737_1760939815_proposal_6859f4fdec7259.46242452.pdf\"]', 'wff bfeq b3 ', 'Approved', '2025-10-26 06:55:20', '2025-10-28 19:01:53', 0),
(28, 45, 1, '2025-10-27', '2025-10-31', 'Approved', 'okey', '2hod', '[\"1761571052_1761445187_att_1747939114_chapter1_aqilah_di230041_aftercorrection.pdf\"]', 'nklnekcl', 'Approved', '2025-10-27 13:15:44', '2025-10-27 13:17:58', 0),
(29, 50, 1, '2025-11-03', '2025-11-07', 'Approved', 'CONYHH', 'RYY', '[\"1761897418_6_proposal_6859f39a9df0a0.48520786.pdf\"]', 'OKEY', 'Approved', '2025-10-31 07:56:35', '2025-10-31 07:57:22', 0),
(30, 37, 7, '2025-11-18', '2025-11-25', 'Belum Hantar', NULL, NULL, NULL, NULL, 'Pending', '2025-11-17 16:45:32', '2025-11-17 16:45:32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `penerima_id` int(11) NOT NULL,
  `penerima_role` enum('pelajar','penyelia','admin') NOT NULL,
  `mesej` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` enum('baru','dibaca') DEFAULT 'baru',
  `tarikh` datetime DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `penerima_id`, `penerima_role`, `mesej`, `link`, `status`, `tarikh`, `created_at`) VALUES
(5, 1, 'pelajar', 'Penyelia telah meluluskan permohonan anda.', 'student_dashboard.php', '', '2025-11-18 02:58:58', '2025-11-18 02:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `pelajar`
--

CREATE TABLE `pelajar` (
  `id_pelajar` int(11) NOT NULL,
  `nama_pelajar` varchar(255) NOT NULL,
  `no_matrik` varchar(20) NOT NULL,
  `program` varchar(100) NOT NULL,
  `emel` varchar(100) NOT NULL,
  `kata_laluan` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefon` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `psm` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelajar`
--

INSERT INTO `pelajar` (`id_pelajar`, `nama_pelajar`, `no_matrik`, `program`, `emel`, `kata_laluan`, `created_at`, `telefon`, `profile_image`, `psm`) VALUES
(2, 'NUR AQILAH BINTI ANNUAR', 'DI230041', 'BIT', 'di230041@student.uthm.edu.my', '$2y$10$MaMZQAMGOxTpMsFQJep4HujMriqd7mOylIzkx0Dk3U3lxXBa/N6oG', '2025-06-18 15:34:59', '0177295830', 'profile_2_1759651800.png', NULL),
(3, 'FARIDAH BINTI MOHD SUPANJI', 'DI240041', 'BIW', 'di240041@student.uthm.edu.my', '$2y$10$tijMrY//XOzAClua6JERqudv8Y2K6nAKzcwPuyx0de3zNV5NKu1vq', '2025-06-23 15:36:26', '0162469184', 'profile_3_1760939454.png', NULL),
(9, 'NUR IZZATI BINTI ANNUAR', 'ce230056', 'BEV', 'ce230056@student.uthm.edu.my', '$2y$10$J30rOwNRz/0OeyJFRWcHfO3s7NKKcxZ0fPRYaRtSjQ84E888G32gS', '2025-10-20 12:51:50', '092992792', 'profile_9_1760964857.png', 'PSM1'),
(10, 'OSMAN', 'CI240043', 'BIT', 'ci240043@student.uthm.edu.my', '$2y$10$fve5Jmo1ivmkk8aiiXffPutJyznB/d8tZcwGJr6ce1IbnoQAx/DQm', '2025-10-21 10:14:34', '01170741087', 'profile_10_1762683016.jpeg', NULL),
(11, 'MUHAMMAD HARAZ BIN ISMAIL', 'DI230045', 'BIS', 'di230045@student.uth.edu.my', '$2y$10$dVD6BtCWaYEvu9dLUXp76uFJuoT35yR/e/XroQgfZXhfGKoRTXJCi', '2025-10-26 08:56:57', '', NULL, NULL),
(12, 'AKMAL BIN ANNUAR', 'AA232365', 'BIM', 'aa232365@student.uthm.edu.my', '$2y$10$5I/AVkYiyWgsbN3WRp.C6uVVcikX1adfpt1UUom77WTWOkI7bo9nS', '2025-10-27 12:54:43', '016-5354123', 'profile_12_1761571746.png', 'PSM1'),
(13, 'QYLAMANIS BINTI OSMAN', 'aa220001', 'BIT', 'aa220001@student.uthm.edu.my', '$2y$10$0U3jWFrCxatgQKanGUIaZO.Zns3nHbM58dxuZNBJpEAEbosIaDK66', '2025-10-27 16:00:58', NULL, NULL, 'PSM1'),
(14, 'ANNUAR BIN ISMAIL', 'di230054', 'BIS', 'di230054@student.uthm.edu.my', '$2y$10$uRkXTowejnL.3Fpr9O8W7e/NHdDtje5xFBL/ZUb/jfuKaLRDYDoSW', '2025-10-30 15:56:51', NULL, NULL, 'PSM1'),
(15, 'SALENAA', 'CI230001', 'BIM', 'ci230001@student.uthm.edu.my', '$2y$10$/nCyIRLmShN2dpT.g/Lr9eGKNddyfB/sF8s3brIbOXn4r7j.AnAM.', '2025-10-31 06:11:54', '017-7295830', 'profile_15_1761892268.jpg', NULL),
(16, 'HAZRIN SHAH', 'DI230067', 'BIT', 'di230067@student.uthm.edu.my', '$2y$10$EZqDvcQEThmCny0e9vy2EeKkAJcAC/noDSiQIPgaZYM0QFf4D/4Cu', '2025-10-31 07:50:07', '0177295830', 'profile_16_1761899792.jpg', NULL),
(17, 'AYU BINTI RAZLAN', 'aa212621', 'BIM', 'aa212621@student.uthm.edu.my', '$2y$10$i22SVn2MtZMNm1l4o7SHIOLFmxGh7FnvHoEb5ejtPadXFqEzVXltO', '2025-11-04 13:49:11', NULL, NULL, 'PSM1'),
(18, 'OSMAN BIN ABU', 'DI240011', 'BIT', 'di240011@student.uthm.edu.my', '$2y$10$x4bbzfgkMIQdVz5mzRo36OhXzwcuH6.JB8iznWaCKzCFVmaDRIHmi', '2025-11-09 03:26:39', NULL, NULL, 'PSM2');

-- --------------------------------------------------------

--
-- Table structure for table `penyelia`
--

CREATE TABLE `penyelia` (
  `id_penyelia` int(11) NOT NULL,
  `kata_laluan` varchar(255) NOT NULL,
  `nama_penyelia` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `jawatan` varchar(100) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `pautan_cv` varchar(255) DEFAULT NULL,
  `kuota` int(11) DEFAULT 5,
  `bidang_kepakaran` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'penyelia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyelia`
--

INSERT INTO `penyelia` (`id_penyelia`, `kata_laluan`, `nama_penyelia`, `email`, `course`, `profile_image`, `jawatan`, `telefon`, `jabatan`, `pautan_cv`, `kuota`, `bidang_kepakaran`, `role`) VALUES
(1, '$2y$10$gH1qsYnsHqR/qZF3nfPbe.TYVJdpl7U0cNv0/YBu8T0wqbQicTauO', 'MOHD ZAKI BIN MOHD SALIKON', 'mdzaki@uthm.edu.my', 'BIP', 'profile_1.jpg', 'DS13 PENSYARAH KANAN', '07-4533648', 'Jabatan Kejuruteraan Perisian', 'https://community.uthm.edu.my/mdzaki', 4, NULL, 'penyelia'),
(2, '$2y$10$R45/Ux0cnlHLERJAdV2ZMeHi6rcQl.4W91B71hBJcvfVxvZNmD9XO', 'PUAN MUNIRAH BINTI MOHD YUSOF', 'munirah@uthm.edu.my', 'BIP', 'profile_2.png', 'DS13 PENSYARAH KANAN', '07-4533769', 'Jabatan Perisian Komputer', 'https://community.uthm.edu.my/munirah', 5, NULL, 'panel'),
(17, '$2y$10$Sm73OfbzI0c2NGkP7DPDe.Mjtgh87YtflsDfJv8TyY9kCCVKx46F6', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', 'yana@uthm.edu.my', 'BIP', 'profile_17.jpg', 'DS14 PROFESOR MADYA', '07-4533722', 'Jabatan Kejuruteraan Perisian', 'https://community.uthm.edu.my/yana', 4, NULL, 'penyelia'),
(18, '$2y$10$mx9MsouY/uYSufOldpTKW.ysssULxBCkSzZERxiceexomJbDClB4S', 'ANNUAR BIN ISMAIL', 'annuarrr@uthm.edu.my', 'bim', NULL, NULL, NULL, NULL, NULL, 4, NULL, 'penyelia');

-- --------------------------------------------------------

--
-- Table structure for table `permohonan`
--

CREATE TABLE `permohonan` (
  `id` int(11) NOT NULL,
  `id_pelajar` int(11) DEFAULT NULL,
  `id_penyelia` int(11) NOT NULL,
  `id_tajuk` int(11) DEFAULT NULL,
  `tajuk1` varchar(255) DEFAULT NULL,
  `abstrak1` text DEFAULT NULL,
  `tajuk2` varchar(255) DEFAULT NULL,
  `abstrak2` text DEFAULT NULL,
  `tajuk3` varchar(255) DEFAULT NULL,
  `abstrak3` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Menunggu',
  `tajuk_dipilih` varchar(255) DEFAULT NULL,
  `komen` text DEFAULT NULL,
  `tarikh_hantar` datetime DEFAULT current_timestamp(),
  `formA` varchar(255) DEFAULT NULL,
  `formB` varchar(255) DEFAULT NULL,
  `formC` varchar(255) DEFAULT NULL,
  `tarikh_hantar_form` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permohonan`
--

INSERT INTO `permohonan` (`id`, `id_pelajar`, `id_penyelia`, `id_tajuk`, `tajuk1`, `abstrak1`, `tajuk2`, `abstrak2`, `tajuk3`, `abstrak3`, `status`, `tajuk_dipilih`, `komen`, `tarikh_hantar`, `formA`, `formB`, `formC`, `tarikh_hantar_form`) VALUES
(37, 2, 2, NULL, 'driving system', '', '', '', '', '', 'Diluluskan', 'driving system', 'okey bolehh , jumpaa sayaa nnti yaaa ', '2025-10-20 11:08:29', 'uploads/student_2/formA_1763387340.pdf', 'uploads/student_2/formB_1763387340.pdf', 'uploads/student_2/formC_1763387340.pdf', NULL),
(38, 3, 1, NULL, 'ayam', 'qqqqq', 'itik', 'wwwww', 'kambingg', 'eeeeeeeeeeee', 'Diluluskan', 'kambingg', 'wokey noteddd. .terus jumpa saya yaaa ', '2025-10-20 13:51:31', 'uploads/student_3/formA_1760939556.pdf', 'uploads/student_3/formB_1760939556.pdf', 'uploads/student_3/formC_1760939556.pdf', NULL),
(39, 9, 17, NULL, 'BUJANG', '111', 'RIMAU', '111', 'MUSANG', '111', 'Diluluskan', 'MUSANG', 'OKEYYYYY', '2025-10-20 20:56:21', 'uploads/student_9/formA_1760965266.pdf', 'uploads/student_9/formB_1760965266.pdf', 'uploads/student_9/formC_1760965266.pdf', NULL),
(40, 10, 1, NULL, 'restaurant system', '', '', '', '', '', 'Diluluskan', 'restaurant system', 'okey jumpaa sayaa', '2025-10-21 18:17:40', 'uploads/student_10/formA_1761041999.pdf', 'uploads/student_10/formB_1761041999.pdf', 'uploads/student_10/formC_1761041999.pdf', NULL),
(44, 11, 2, NULL, 'exora', 'acpascnmpsc', 'myvi', 'nqsop', 'kelisa', 'qjns ciqp', 'Diluluskan', 'kelisa', 'okey noted bohhh ', '2025-10-26 20:43:07', 'uploads/student_11/formA_1761484242.pdf', 'uploads/student_11/formB_1761484242.pdf', 'uploads/student_11/formC_1761484242.pdf', NULL),
(45, 12, 2, NULL, 'musang', 'qoilnio', 'bujang', 'wkncqem;', 'rimau', 'eqlnfen', 'Diluluskan', 'bujang', 'OKEY LULUS', '2025-10-27 20:59:37', 'uploads/student_12/formA_1761570167.pdf', 'uploads/student_12/formB_1761570167.pdf', 'uploads/student_12/formC_1761570167.pdf', NULL),
(46, 13, 2, NULL, 'driving system', '', '', '', '', '', 'Ditolak', NULL, 'reject sbb dah sama', '2025-10-28 00:01:29', NULL, NULL, NULL, NULL),
(47, 13, 2, NULL, 'supervisor system psm fsktm ', '', '', '', '', '', 'Diluluskan', 'supervisor system psm fsktm ', 'okkeyoo', '2025-10-28 00:24:12', 'uploads/student_13/formA_1761582637.pdf', 'uploads/student_13/formB_1761582637.pdf', 'uploads/student_13/formC_1761582637.pdf', NULL),
(48, 14, 2, NULL, 'AYAM ITIKK KAMBING', '', '', '', '', '', 'Diluluskan', 'AYAM ITIKK KAMBING', 'hbciestrdytfgyuhuijioko', '2025-10-30 23:57:22', 'uploads/student_14/formA_1761840587.pdf', 'uploads/student_14/formB_1761840587.pdf', 'uploads/student_14/formC_1761840587.pdf', NULL),
(49, 15, 17, NULL, 'bangloww', 'enfiejfpo3rj2[', 'teres', 'enimjcv[q3pkc[3\'', 'pondok', 'enofin3rfo3;r', 'Diluluskan', 'bangloww', 'wokeyooo ', '2025-10-31 14:12:49', 'uploads/student_15/formA_1761891232.pdf', 'uploads/student_15/formB_1761891232.pdf', 'uploads/student_15/formC_1761891232.pdf', NULL),
(50, 16, 17, NULL, 'System Java Programming', '', '', '', '', '', 'Diluluskan', 'System Java Programming', 'OKEY', '2025-10-31 15:50:54', 'uploads/student_16/formA_1761897166.pdf', 'uploads/student_16/formB_1761897166.pdf', 'uploads/student_16/formC_1761897166.pdf', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sejarah_tajuk`
--

CREATE TABLE `sejarah_tajuk` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `supervisor` varchar(255) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `authors` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sejarah_tajuk`
--

INSERT INTO `sejarah_tajuk` (`id`, `title`, `supervisor`, `year`, `course`, `authors`) VALUES
(1061, 'TutorWave: Tutor Selection Management System', 'PROF. Dr. ABD SAMAD BIN HASAN BASARI', '2023', '.', ''),
(1062, 'Development of a Restaurant Recommendation System Based on Sentiment Analysis in Johor', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1063, 'EMPLOYEE MANAGEMENT SYSTEM SANTAI ILMU PUBLICATION', 'PROF. Dr. NAZRI BIN MOHD NAWI', '2023', '.', ''),
(1064, 'SISTEM PEMANTAUAN KELEMBAPAN TANAH TANAMAN DAN KELEMBAPAN UDARA', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1065, 'E-BILIK SISTEM TEMPAHAN BILIK BELAJAR PERPUSTAKAAN TUNKU TUN AMINAH UNIVERSITI TUN HUSSEIN ONN MALAYSIA', 'PUAN NORLIDA BINTI HASSAN', '2023', '.', ''),
(1066, 'SIREH PARK ACTIVITY RESERVATION SYSTEM', 'PROF. Dr. NAZRI BIN MOHD NAWI', '2023', '.', ''),
(1067, 'Sistem Pengurusan Kenderaan Pejabat Majlis Daerah Tanah Merah (SPKP MDTM)', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1068, 'Strawberry Farm Information Management System', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1069, 'Academic Tutor Booking System ', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1070, 'Malaysian History Portal', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1071, 'Pengurusan Rumah Sewa Mulot', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1072, 'Topi Keselamatan Pekerja Berasaskan IoT Untuk Mengesan Paras Gas Toksik', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1073, 'SISTEM PENGURUSAN PERKHIDMATAN MALIM GUNUNG PERHUTANAN (MGP)', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1074, 'UTHMWay: Campus Navigation System', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1075, 'Inventory Management System for Yeo Plumber Sdn Bhd', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1076, 'Attendance Tracking System with RFID for Form 6', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1077, 'Hospital Blood bank management system ', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1078, 'Food Ordering System in the School Canteen for Teachers', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1079, 'BOOKING APPLICATION FOR HIKING GUNUNG LEDANG', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1080, 'SportStream', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1081, 'Sistem Akuaponik Pintar Berasaskan Internet Benda (IoT)', 'PROF. MADYA Ts. Dr. AZIZUL AZHAR BIN RAMLI', '2023', '.', ''),
(1082, 'CarGoWash System ', 'PROF. Dr. ABD SAMAD BIN HASAN BASARI', '2023', '.', ''),
(1083, 'MySurf Technology Inventory Management System ', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1084, 'RAW CHICKEN ORDERING SYSTEM', 'PROF. Dr. NAZRI BIN MOHD NAWI', '2023', '.', ''),
(1085, 'Sistem Pesanan Makanan Berasaskan Aplikasi Mudah Alih Kedai Makan Azizah Corner ', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1086, 'WEB-BASED BUSINESS MANAGEMENT SYSTEM FOR LOVILICIOUS SURPRISE DELIVERY', 'Dr. NUR LIYANA BINTI SULAIMAN', '2023', '.', ''),
(1087, 'TroubleShot - Troubleshooting Hub', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1088, 'Sistem Pengurusan Kelas Tambahan 3 Lang (Cemerlang Gemilang Terbilang)', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1089, 'MushMagic-Mushroom ordering Application', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1090, 'Pablo\'s Self-Serve Food Ordering System with Virtual Waitlist Management', 'Dr. MOHD HAMDI IRWAN BIN HAMZAH', '2023', '.', ''),
(1091, 'Blood Bank Volunteer Application', 'Dr. NUREZAYANA BINTI ZAINAL', '2023', '.', ''),
(1092, 'Zaim Catering Booking System', 'PROF. Dr. ROZAIDA BINTI GHAZALI', '2023', '.', ''),
(1093, 'Food Ordering System for My Bubble Time Cafe ', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1094, 'Tool and Equipment Information Management', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1095, 'FIRE: Fast-Tracking Integrated Research?Explore', 'Ts. Dr. SHUHAIDA BINTI ISMAIL', '2023', '.', ''),
(1096, 'PROMOTER INFORMATION MANAGEMENT SYSTEM', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1097, 'Biddo: Property Rental Bidding System', 'Dr. SUHAILA BINTI MOHD. YASIN', '2023', '.', ''),
(1098, 'Mah Heng Motor Enterprise Full Stack Management System', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1099, 'Lidy Bakery Ordering System', 'PROF. Dr. ABD SAMAD BIN HASAN BASARI', '2023', '.', ''),
(1100, 'Talam Cake Ordering System', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1101, 'KINDERGARTEN MANAGEMENT SYSTEM', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1102, 'Freshwater Prawn Management System', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1103, 'Alumni Monitoring System', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1104, 'MUSIC CHORDS RECOGNITION USING ARTIFICIAL INTELLIGENCE TECHNIQUE ', 'PROF. Dr. ABD SAMAD BIN HASAN BASARI', '2023', '.', ''),
(1105, 'Autism Learning Mobile Application', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1106, 'web-based management system for UTHM events management', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1107, 'AirGuardian: Real-Time IoT-Based Air Pollutant Monitor System', 'Ts. Dr. SUZIYANTI BINTI MARJUDI', '2023', '.', ''),
(1108, 'SISTEM PENGURUSAN PENGHANTARAN DAN INVENTORI PERALATAN TIDUR', 'Dr. MOHAMAD FIRDAUS BIN AB. AZIZ', '2023', '.', ''),
(1109, 'Sistem Pengurusan Penjualan Butik Roti Maria', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1110, 'APLIKASI TEMPAT LETAK KENDERAAN PINTAR SECUREPARK UNTUK KOLEJ PERWIRA UTHM', 'Dr. MOHAMAD FIRDAUS BIN AB. AZIZ', '2023', '.', ''),
(1111, 'UTHMAssist Online Job Portal', 'Ts. Dr. SUZIYANTI BINTI MARJUDI', '2023', '.', ''),
(1112, 'Office Project Management System \"PrOffice\"', 'Ts. Dr. SUZIYANTI BINTI MARJUDI', '2023', '.', ''),
(1113, 'Recommendation System for Tenant in Kuala Lumpur', ' Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1114, 'MuzikML: Sistem Pengesyoran Muzik Menggunakan ?Machine Language?', 'Ts. Dr. MOHD AMIN BIN MOHD YUNUS', '2023', '.', ''),
(1115, 'Alumni tracking and Engagement System ', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1116, 'Sistem Pesanan Makanan Oden Cikgu', 'Ts. Dr. MOHD AMIN BIN MOHD YUNUS', '2023', '.', ''),
(1117, 'Syed & Co. sistem penyewaan kereta', 'Dr. NUR LIYANA BINTI SULAIMAN', '2023', '.', ''),
(1118, 'Aplikasi Pengurusan Kesihatan dan Perubatan Pesakit Dewasa', 'Dr. MOHAMAD FIRDAUS BIN AB. AZIZ', '2023', '.', ''),
(1119, 'Sistem Pengurusan Persidangan Dewan Perwakilan Mahasiswa UTHM (e-Sidang)', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1120, 'LEARNING MANAGEMENT SYSTEM: UTHM CLASS HUB', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1121, 'DN Wedding Planning Booking System', 'Ts. Dr. MOHD AMIN BIN MOHD YUNUS', '2023', '.', ''),
(1122, 'Online Curriculum Evaluation System', 'Ts. Dr. SUZIYANTI BINTI MARJUDI', '2023', '.', ''),
(1123, 'SISTEM PENGURUSAN PERSATUAN IBU BAPA DAN GURU SEKOLAH KEBANGSAAN JALAN MATANG BULUH', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1124, 'Machine Learning Anomaly-Detection to monitor and analyze Cybersecurity different threats and attacks in Malaysia', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1125, 'Aplikasi Panduan Pemilihan Komponen PC Berdasarkan Perisian', 'Dr. MOHAMAD FIRDAUS BIN AB. AZIZ', '2023', '.', ''),
(1126, 'Family Planning Information System Management', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1127, 'SISTEM PENGURUSAN PERPUSTAKAAN SEKOLAH KEBANGSAAN MENTUAN', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1128, 'PARCEL HUB FOR UTHM RESIDENTIAL COLLEGE MANAGEMENT SYSTEM', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1129, 'LORONG RASIMAH HOUSE RENTAL MANAGEMENT SYSTEM', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1130, 'BanjirParkGuard: Sistem Pemerhatian dan Amaran Banjir Tempat Letak Kenderaan Bawah Tanah Centerpoint Seremban', 'PROF. MADYA Ts. Dr. AZIZUL AZHAR BIN RAMLI', '2023', '.', ''),
(1131, 'ATC BAKERY BOOKING SYSTEM', 'PROF. Dr. NAZRI BIN MOHD NAWI', '2023', '.', ''),
(1132, 'ERA BEAUTY SPA BOOKING SYSTEM', 'PROF. Dr. NAZRI BIN MOHD NAWI', '2023', '.', ''),
(1133, 'PARCEL MANAGEMENT SYSTEM IN KASTAM PULAU INDAH HOUSING', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1134, 'Autism Spectrum E-Learning Portal for Skill Enhancement and Support', 'Dr. MOHD HAMDI IRWAN BIN HAMZAH', '2023', '.', ''),
(1135, 'QR- CODE PRESCHOOLER DISMISSAL MANAGEMENT SYSTEM', 'Dr. NUR ARIFFIN BIN MOHD ZIN', '2023', '.', ''),
(1136, 'Library Management System for SK Panchor', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1137, 'Aplikasi Tempahan Gelanggang Pura Kencana', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1138, 'SKINCARE RECOMMENDATION SYSTEM', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1139, 'Ivory Inn Room Reservation System', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1140, 'Sistem Pemantauan Perkembangan Anak - Anak Autisme', 'PUAN NORLIDA BINTI HASSAN', '2023', '.', ''),
(1141, 'STUDENT NOMINATION SYSTEM FOR FSKTM', 'Dr. MUHAINI BINTI OTHMAN', '2023', '.', ''),
(1142, 'PURCHASE ORDER SYSTEM USING QR CODE FOR CENDOL CLAYPOT', 'PUAN HANNANI BINTI AMAN', '2023', '.', ''),
(1143, 'SISTEM PANTAUAN KESIHATAN PRANATAL', 'Dr. NORHAMREEZA BINTI ABDUL HAMID', '2023', '.', ''),
(1144, 'SISTEM PENGURUSAN OPERASI PUSAT CUCI KERETA DW GARAJ', 'PUAN NORLIDA BINTI HASSAN', '2023', '.', ''),
(1145, 'Sistem Pengurusan Projek Sarjana Muda bagi FSKTM', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1146, 'Livestock Management System for Bistari Farm', 'PUAN HANNANI BINTI AMAN', '2023', '.', ''),
(1147, 'Online Appointment System for Senior Citizen Checkups', 'PROF. Dr. ROZAIDA BINTI GHAZALI', '2023', '.', ''),
(1148, 'SpainHub: Web-Based Spanish Learning Language', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1149, 'Rental House Hub Mobile Application ', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1150, 'Sistem Pengurusan Penjualan dan Pembelian Hijrah Palm Enterprise', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1151, 'SISTEM PESANAN MAKANAN DALAM TALIAN UNIVERSITI TUN HUSSEIN ONN MALAYSIA (UTHM)', 'PROF. MADYA Dr. NOOR AZAH BINTI SAMSUDIN', '2023', '.', ''),
(1152, 'EDUCATIONAL RESOURCES WEBSITE FOR SMK ULU TIRAM', 'Dr. MOHD ZANES BIN SAHID', '2023', '.', ''),
(1153, 'LA PADELLA WESTERN & ITALIAN RESTAURANT\r\nFOOD ORDERING SYSTEM', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1154, 'Online Booking Appointment System for Klinik Afiyah', 'Dr. NORHAMREEZA BINTI ABDUL HAMID', '2023', '.', ''),
(1155, 'Classification Types of Pineapple Application', 'PUAN HANNANI BINTI AMAN', '2023', '.', ''),
(1156, 'Sistem Laporan Pemantauan Kanak-Kanak Prasekolah Sekolah Kebangsaan Sungai Mas', 'Dr. MOHD HAMDI IRWAN BIN HAMZAH', '2023', '.', ''),
(1157, 'WEB-BASED LEARNING JAWI USING SIGN LANGUAGE ', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1158, 'EasyKitarHub: UTHM Wave For Sustainability', 'Dr. SITI HAJAR BINTI ARBAIN', '2023', '.', ''),
(1159, 'APLIKASI PEMBELAJARAN MUDAH ALIH BAHASA MELAYU', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1160, 'FKAAB Laboratory Booking System', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1161, 'Online Damage Complaint Management System for Kolej Kediaman Tun Fatimah', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1162, 'UTHM Lost and Found Management System', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1163, 'BITARA MART CHICKEN AND MEAT ORDERING SYSTEM', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1164, 'Bicycle Rental Management System for UTHM Student', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1165, 'ARSMA RESOURCE\'S EMPLOYEE DATA AND SALARY MANAGEMENT SYSTEM', 'Dr. ROZITA BINTI ABDUL JALIL', '2023', '.', ''),
(1166, 'KARATE GRADING JUDGING SYSTEM', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1167, 'SISTEM PENGURUSAN BENGKEL PEMBELAJARAN SEPANJANG HAYAT KOLEJ KOMUNITI SELANDAR', 'Dr. MOHD HAMDI IRWAN BIN HAMZAH', '2023', '.', ''),
(1168, 'PERWIRA CHRONICLES FOODIES', 'Ts. Dr. MOHD AMIN BIN MOHD YUNUS', '2023', '.', ''),
(1169, 'FloodWise - Flood Alert and Monitoring System', 'Ts. Dr. SUZIYANTI BINTI MARJUDI', '2023', '.', ''),
(1170, 'Career Integrated Resolution System ', 'Ts. Dr. SHUHAIDA BINTI ISMAIL', '2023', '.', ''),
(1171, 'GOAT FARM INFORMATION MANAGEMENT SYSTEM', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', ''),
(1172, 'MYRELASIS UTHM DIGITAL EPORTAL: DEVELOPMENT OF RELASIS STUDENT INFORMATION SYSTEM', 'Dr. RADIAH BINTI MOHAMAD', '2023', '.', ''),
(1173, 'SISTEM TEMPAHAN SERVIS KERETA', 'PUAN NORLIDA BINTI HASSAN', '2023', '.', ''),
(1174, 'UTHM STUDENT DRIVER HUB BOOKING SYSTEM', 'ENCIK MOHD ZAKI BIN MOHD SALIKON', '2023', '.', ''),
(1175, 'Language Disorders Therapy Mobile Application for Preschoolers', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1176, 'FOOD DONATION MANAGEMENT SYSTEM', 'PUAN MUNIRAH BINTI MOHD YUSOF', '2023', '.', ''),
(1177, 'TravelPlanner Management System for BeeFunVacay', 'PROF. Dr. ROZAIDA BINTI GHAZALI', '2023', '.', ''),
(1178, 'NOVEL\'S MARKETPLACE WEBSITE', 'PUAN ROZLINI BINTI MOHAMED', '2023', '.', ''),
(1179, 'Shop Management System for Top MeeraSiva Enterprise', 'Dr. YANA MAZWIN BINTI MOHMAD HASSIM', '2023', '.', ''),
(1180, 'LAW FIRM MANAGEMENT SYSTEM', 'Dr. MOHAMAD AIZI BIN SALAMAT', '2023', '.', ''),
(1181, 'FLYHEALTH: AVIATION MEDICAL MANAGEMENT AND SERVICES', 'Dr. SUHAILA BINTI MOHD. YASIN', '2023', '.', ''),
(1182, 'UTHM FOODIE: CAFE FOOD ORDERING SYSTEM', 'Dr. SUHAILA BINTI MOHD. YASIN', '2023', '.', ''),
(1183, 'CUSTOMER BILLING SYSTEM FOR EXCAVATOR?WORKSHOP', 'PROF. MADYA Dr. NUREIZE BINTI ARBAIY', '2023', '.', '');

-- --------------------------------------------------------

--
-- Table structure for table `tajuk_penyelia`
--

CREATE TABLE `tajuk_penyelia` (
  `id` int(11) NOT NULL,
  `id_penyelia` int(11) NOT NULL,
  `tajuk` varchar(255) NOT NULL,
  `abstrak` text DEFAULT NULL,
  `bidang` varchar(150) DEFAULT NULL,
  `status` enum('aktif','tidak aktif') DEFAULT 'aktif',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tajuk_penyelia`
--

INSERT INTO `tajuk_penyelia` (`id`, `id_penyelia`, `tajuk`, `abstrak`, `bidang`, `status`, `keterangan`) VALUES
(3, 2, 'supervisor system psm fsktm ', 'INI BUAT SYSTEM PSM', 'BIP', 'aktif', 'KENA BUATTTTH ETH TE '),
(4, 2, 'driving system', 'INI DRIVING SYSTEM', 'bip', 'aktif', 'ewtweyhwy'),
(6, 1, 'ai data mining ', 'qfnlq;fneqg;', 'bip', 'aktif', 'wjfjqoefw'),
(7, 1, 'restaurant system', 'makanan dan air DSBSBSFNFSN', '3rpv;', 'aktif', 'WAJIB ADA'),
(8, 2, 'AYAM ITIKK KAMBING', 'INI AYAM ITIK KAMBING', 'BIP', 'aktif', 'VH4UBNUI4TMBIPOBK,5O'),
(9, 2, 'KERETA API APPS', 'INI KERETA API APPS', 'BIM', 'aktif', 'YN5WJNW5'),
(10, 17, 'System Java Programming', 'uhfoiwhgpwjqr[', 'BIM', 'aktif', 'EGTHJ46');

-- --------------------------------------------------------

--
-- Table structure for table `temujanji`
--

CREATE TABLE `temujanji` (
  `id` int(11) NOT NULL,
  `id_pelajar` int(11) NOT NULL,
  `id_penyelia` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `tarikh` date NOT NULL,
  `masa` time NOT NULL,
  `tujuan` text DEFAULT NULL,
  `status` enum('Dalam Proses','Diluluskan','Ditolak','Dibatalkan') DEFAULT 'Dalam Proses',
  `komen` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temujanji`
--

INSERT INTO `temujanji` (`id`, `id_pelajar`, `id_penyelia`, `nama`, `email`, `tarikh`, `masa`, `tujuan`, `status`, `komen`, `created_at`) VALUES
(14, 2, 2, 'NUR AQILAH BINTI ANNUAR', 'di230041@student.uthm.edu.my', '2025-10-21', '03:20:00', 'nakk buat google meet ', 'Diluluskan', 'noteddd', '2025-10-20 03:21:04'),
(15, 2, 2, 'NUR AQILAH BINTI ANNUAR', 'di230041@student.uthm.edu.my', '2025-10-21', '03:22:00', 'lr7l7', 'Diluluskan', 'okey noted okey note', '2025-10-20 03:23:01'),
(16, 3, 1, 'FARIDAH BINTI MOHD SUPANJI', 'di240041@student.uthm.edu.my', '2025-10-27', '17:52:00', 'saya nkk jumpaaa sir boleh ?', 'Diluluskan', 'OKEY BOLEH', '2025-10-20 05:53:01'),
(17, 9, 17, 'nur izzati', 'ce230056@student.uthm.edu.my', '2025-10-21', '10:02:00', 'saya nkk ckp psl proposal', 'Diluluskan', 'okeyy bolehhh', '2025-10-20 13:04:07'),
(18, 3, 1, 'FARIDAH BINTI MOHD SUPANJI', 'di240041@student.uthm.edu.my', '2025-10-22', '15:09:00', 'djoqwfekqpgj[qep', 'Diluluskan', '2nogno2;ngp;4ngp', '2025-10-20 15:09:37'),
(19, 3, 1, '', '', '2025-10-21', '02:18:00', 'fhi23jwjgpqo', 'Diluluskan', 'wvv', '2025-10-20 15:16:30'),
(20, 10, 1, '', '', '2025-10-23', '09:21:00', 'saya nk bincang proposal', 'Diluluskan', 'okey saya tunggu', '2025-10-21 10:21:22'),
(21, 12, 2, '', '', '2025-10-28', '12:05:00', 'DR SAYA NK JUMPAA', 'Diluluskan', 'OKEY NOTEDD', '2025-10-27 13:06:06'),
(22, 11, 2, '', '', '2025-10-30', '04:39:00', 'R3QG3RQB', 'Diluluskan', 'okey lulussss', '2025-10-28 17:39:14'),
(23, 11, 2, '', '', '2025-10-27', '01:57:00', 'BT4B5Y2N5Y2', 'Ditolak', 'TOLAK SAYA ADA HAL', '2025-10-28 17:52:36'),
(24, 15, 17, '', '', '2025-10-31', '18:14:00', 'rwvbqb', 'Diluluskan', 'wokeyoo notedd', '2025-10-31 06:14:32'),
(25, 16, 17, '', '', '2025-11-01', '11:58:00', 'OKEYY SAYA NK JUMPAA DR ', 'Ditolak', 'SAYA TAK NK JYMPPA AWAK', '2025-10-31 07:53:52'),
(26, 2, 2, '', '', '2025-11-18', '13:49:00', 'wefweg', 'Dalam Proses', NULL, '2025-11-17 13:49:12'),
(27, 2, 2, '', '', '2025-11-27', '14:51:00', 'grb3q', 'Dalam Proses', NULL, '2025-11-17 13:51:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `chapter`
--
ALTER TABLE `chapter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logbook`
--
ALTER TABLE `logbook`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permohonan_id` (`permohonan_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`);

--
-- Indexes for table `pelajar`
--
ALTER TABLE `pelajar`
  ADD PRIMARY KEY (`id_pelajar`);

--
-- Indexes for table `penyelia`
--
ALTER TABLE `penyelia`
  ADD PRIMARY KEY (`id_penyelia`);

--
-- Indexes for table `permohonan`
--
ALTER TABLE `permohonan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sejarah_tajuk`
--
ALTER TABLE `sejarah_tajuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tajuk_penyelia`
--
ALTER TABLE `tajuk_penyelia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penyelia` (`id_penyelia`);

--
-- Indexes for table `temujanji`
--
ALTER TABLE `temujanji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_penyelia` (`id_penyelia`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapter`
--
ALTER TABLE `chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `info`
--
ALTER TABLE `info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelajar`
--
ALTER TABLE `pelajar`
  MODIFY `id_pelajar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `penyelia`
--
ALTER TABLE `penyelia`
  MODIFY `id_penyelia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;

--
-- AUTO_INCREMENT for table `permohonan`
--
ALTER TABLE `permohonan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sejarah_tajuk`
--
ALTER TABLE `sejarah_tajuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1184;

--
-- AUTO_INCREMENT for table `tajuk_penyelia`
--
ALTER TABLE `tajuk_penyelia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `temujanji`
--
ALTER TABLE `temujanji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logbook`
--
ALTER TABLE `logbook`
  ADD CONSTRAINT `logbook_ibfk_1` FOREIGN KEY (`permohonan_id`) REFERENCES `permohonan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tajuk_penyelia`
--
ALTER TABLE `tajuk_penyelia`
  ADD CONSTRAINT `tajuk_penyelia_ibfk_1` FOREIGN KEY (`id_penyelia`) REFERENCES `penyelia` (`id_penyelia`);

--
-- Constraints for table `temujanji`
--
ALTER TABLE `temujanji`
  ADD CONSTRAINT `fk_penyelia` FOREIGN KEY (`id_penyelia`) REFERENCES `penyelia` (`id_penyelia`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
