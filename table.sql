-- 1. Academic Unit
CREATE TABLE Academic_Unit (
    AcademicID INT PRIMARY KEY,
    Acad_Name VARCHAR(100),
    Acad_Email VARCHAR(100),
    Acad_Number VARCHAR(20)
);

-- 2. Person-In-Charge
CREATE TABLE Person_In_Charge (
    PicID INT PRIMARY KEY,
    Pic_Name VARCHAR(100),
    Pic_Email VARCHAR(100),
    Program_Desc TEXT
);

-- 3. Student
CREATE TABLE Student (
    StudentID INT PRIMARY KEY,
    Stud_Name VARCHAR(100),
    Stud_MatricNo VARCHAR(50),
    Stud_Email VARCHAR(100),
    Stud_Resume TEXT,
    AcademicID INT,
    PicID INT,
    FOREIGN KEY (AcademicID) REFERENCES Academic_Unit(AcademicID),
    FOREIGN KEY (PicID) REFERENCES Person_In_Charge(PicID)
);

-- 4. Employer
CREATE TABLE Employer (
    EmployerID INT PRIMARY KEY,
    Comp_Name VARCHAR(100),
    Address TEXT,
    Comp_RegistrationNo VARCHAR(50),
    PicID INT,
    FOREIGN KEY (PicID) REFERENCES Person_In_Charge(PicID)
);

-- 5. Internship Listings
CREATE TABLE Intern_Listings (
    InternshipID INT PRIMARY KEY,
    Int_Location VARCHAR(100),
    Int_Duration VARCHAR(50),
    Int_Industry VARCHAR(100),
    Int_Position VARCHAR(100),
    Int_Program VARCHAR(100),
    Int_Details TEXT,
    EmployerID INT,
    FOREIGN KEY (EmployerID) REFERENCES Employer(EmployerID)
);

-- 6. Student Application
CREATE TABLE Student_Application (
    StudentID INT,
    InternshipID INT,
    App_Date DATE,
    App_Status VARCHAR(50),
    EmployerID INT,
    PRIMARY KEY (StudentID, InternshipID),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (InternshipID) REFERENCES InternListings(InternshipID),
    FOREIGN KEY (EmployerID) REFERENCES Employer(EmployerID)
);
