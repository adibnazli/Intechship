-- 1. Academic Unit
CREATE TABLE academic_unit (
    AcademicID INT PRIMARY KEY,
    Acad_Name VARCHAR(100),
    Acad_Email VARCHAR(100),
    Acad_Number VARCHAR(20)
);

-- 2. Person-In-Charge
CREATE TABLE person_in_charge (
    PicID INT PRIMARY KEY,
    Pic_Name VARCHAR(100),
    Pic_Email VARCHAR(100),
    Program_Desc TEXT
);

-- 3. Student
CREATE TABLE student (
    StudentID INT PRIMARY KEY,                
    Stud_Name VARCHAR(100),                      
    Stud_MatricNo VARCHAR(50),
    Stud_Phone VARCHAR(30),                    
    Stud_Programme VARCHAR(100),                   
    Stud_Email VARCHAR(100),                       
    Stud_ResumePath VARCHAR(255),                  
    PicID INT,                                     
    FOREIGN KEY (PicID) REFERENCES Person_In_Charge(PicID)
);


-- 4. Employer
CREATE TABLE employer (
    EmployerID INT PRIMARY KEY,
    Comp_Name VARCHAR(100),
    Address TEXT,
    Comp_RegistrationNo VARCHAR(50),
    PicID INT,
    FOREIGN KEY (PicID) REFERENCES Person_In_Charge(PicID)
);

-- 5. Internship Listings
CREATE TABLE intern_listings (
    InternshipID INT AUTO_INCREMENT PRIMARY KEY,
    Int_Position VARCHAR(255),
    Int_State VARCHAR(100),
    Int_City VARCHAR(100),
    Int_Programme TEXT,
    Int_Allowance DECIMAL(10, 2),
    Int_Details TEXT,
    EmployerID INT,
    PostedAt DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (EmployerID) REFERENCES employers(EmployerID)
);

-- 6. Student Application
CREATE TABLE student_application (
    StudentID INT,
    InternshipID INT,
    App_Date DATE DEFAULT (CURRENT_DATE),
    App_Status VARCHAR(20), 
    EmployerID INT,
    PRIMARY KEY (StudentID, InternshipID),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (InternshipID) REFERENCES intern_listings(InternshipID),
    FOREIGN KEY (EmployerID) REFERENCES Employer(EmployerID)
);
