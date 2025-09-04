-- Create classes table
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(50) NOT NULL,
  `class_code` varchar(20) UNIQUE NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create subjects table
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL,
  `subject_code` varchar(20) UNIQUE NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create terms table
CREATE TABLE IF NOT EXISTS `terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_name` varchar(50) NOT NULL,
  `term_code` varchar(20) UNIQUE NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample data for classes
INSERT INTO `classes` (`class_name`, `class_code`, `description`) VALUES
('Class 1', 'C001', 'First Grade Class'),
('Class 2', 'C002', 'Second Grade Class'),
('Class 3', 'C003', 'Third Grade Class'),
('Class 4', 'C004', 'Fourth Grade Class'),
('Class 5', 'C005', 'Fifth Grade Class'),
('Class 6', 'C006', 'Sixth Grade Class');

-- Insert sample data for subjects
INSERT INTO `subjects` (`subject_name`, `subject_code`, `description`) VALUES
('Mathematics', 'MATH', 'Mathematics and Numbers'),
('English', 'ENG', 'English Language and Literature'),
('Science', 'SCI', 'General Science'),
('History', 'HIST', 'World History'),
('Geography', 'GEO', 'Geography and Maps'),
('Literature', 'LIT', 'Literature and Reading'),
('Art', 'ART', 'Creative Arts'),
('Physical Education', 'PE', 'Physical Education and Sports'),
('Music', 'MUSIC', 'Music and Singing'),
('Computer Science', 'CS', 'Computer and Technology');

-- Insert sample data for terms
INSERT INTO `terms` (`term_name`, `term_code`, `start_date`, `end_date`) VALUES
('First Term', 'T1', '2024-09-01', '2024-12-15'),
('Second Term', 'T2', '2025-01-15', '2025-04-30'),
('Third Term', 'T3', '2025-05-15', '2025-08-31'); 