-- ============================================================
--  Hospital Management System — Database Schema
--  Run this file once to create all tables
--  MySQL 5.7+ / MariaDB 10.2+
-- ============================================================

CREATE DATABASE IF NOT EXISTS hospital_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hospital_db;

-- ------------------------------------------------------------
--  Staff
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Staff (
  staffid   INT          NOT NULL AUTO_INCREMENT,
  firstname VARCHAR(100) NOT NULL,
  surname   VARCHAR(100) NOT NULL,
  PRIMARY KEY (staffid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Doctor
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Doctor (
  doctorid       INT          NOT NULL AUTO_INCREMENT,
  doctorname     VARCHAR(150) NOT NULL,
  specialization VARCHAR(150) NOT NULL,
  PRIMARY KEY (doctorid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  InPatient
--  admissionID is AUTO_INCREMENT — never supplied by the client
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS InPatient (
  admissionID   INT          NOT NULL AUTO_INCREMENT,
  admissionDate DATE         NOT NULL,
  patientName   VARCHAR(150) NOT NULL,
  gender        ENUM('Male','Female') NOT NULL,
  staffid       INT          NOT NULL,
  doctorid      INT          NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (admissionID),
  FOREIGN KEY (staffid)   REFERENCES Staff(staffid),
  FOREIGN KEY (doctorid)  REFERENCES Doctor(doctorid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Room  (one room per admission)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Room (
  roomId      INT            NOT NULL AUTO_INCREMENT,
  roomno      VARCHAR(20)    NOT NULL,
  roomcost    DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  admissionID INT            NOT NULL,
  PRIMARY KEY (roomId),
  FOREIGN KEY (admissionID) REFERENCES InPatient(admissionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  InPatientBill
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS InPatientBill (
  paymentid   INT           NOT NULL AUTO_INCREMENT,
  admissionID INT           NOT NULL,
  total       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (paymentid),
  FOREIGN KEY (admissionID) REFERENCES InPatient(admissionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  InPatientService  (referenced in cascade delete)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS InPatientService (
  serviceId   INT           NOT NULL AUTO_INCREMENT,
  admissionID INT           NOT NULL,
  description VARCHAR(255)  NOT NULL,
  cost        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (serviceId),
  FOREIGN KEY (admissionID) REFERENCES InPatient(admissionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  InPatientMedical  (referenced in cascade delete)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS InPatientMedical (
  medicalId   INT           NOT NULL AUTO_INCREMENT,
  admissionID INT           NOT NULL,
  medicinename VARCHAR(150) NOT NULL,
  PRIMARY KEY (medicalId),
  FOREIGN KEY (admissionID) REFERENCES InPatient(admissionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  OutPatient
--  patientid is AUTO_INCREMENT — never supplied by the client
--  doctorname column removed — fetched via JOIN on doctorid
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS OutPatient (
  patientid   INT          NOT NULL AUTO_INCREMENT,
  patientname VARCHAR(150) NOT NULL,
  staffid     INT          NOT NULL,
  doctorid    INT          NOT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (patientid),
  FOREIGN KEY (staffid)  REFERENCES Staff(staffid),
  FOREIGN KEY (doctorid) REFERENCES Doctor(doctorid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  OutPatientBill  (patientId — consistent casing fixed)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS OutPatientBill (
  billId    INT           NOT NULL AUTO_INCREMENT,
  patientId INT           NOT NULL,
  total     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (billId),
  FOREIGN KEY (patientId) REFERENCES OutPatient(patientid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  OutPatientMedicalInfo
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS OutPatientMedicalInfo (
  id           INT          NOT NULL AUTO_INCREMENT,
  patientId    INT          NOT NULL,
  medicinename VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (patientId) REFERENCES OutPatient(patientid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Sample seed data (optional — comment out if not needed)
-- ------------------------------------------------------------
INSERT IGNORE INTO Staff (staffid, firstname, surname) VALUES
  (1, 'Amina',  'Khan'),
  (2, 'Bilal',  'Raza'),
  (3, 'Fatima', 'Sheikh');

INSERT IGNORE INTO Doctor (doctorid, doctorname, specialization) VALUES
  (1, 'Dr. Hamid Nawaz',  'Cardiology'),
  (2, 'Dr. Sara Malik',   'Neurology'),
  (3, 'Dr. Usman Ghani',  'Orthopedics');
