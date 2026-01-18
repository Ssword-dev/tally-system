## 1. foundation / bootstrap

- [] create a single db connection factory (pdo)
- [] enable strict sql mode
- [] turn on foreign key enforcement
- [] define a global config object (db, app, paths)
- [] create a simple dependency container (array is fine)
- [] define a base exception hierarchy

  - [] forbiddenException
  - [] notFoundException
  - [] validationException

---

## 2. authentication (teachers)

- [ ] create password hashing helper
- [✅] implement teacher registration

  - [ ] validate email uniqueness
  - [✅] hash password
  - [✅] insert into `teachers_tbl`

- [✅] implement teacher login

  - [✅] verify password
  - [✅] start session

- [✅] store `teacher_id` in session
- [ ] write `currentTeacherId()` helper
- [ ] write `requireAuth()` middleware

---

## 3. authorization (ownership model)

- [ ] implement `ownsClass(classId, teacherId)`
- [ ] implement `ownsStudent(studentId, teacherId)`
- [ ] implement `ownsActivityType(typeId, teacherId)`
- [ ] implement `ownsActivity(activityId, teacherId)`
- [ ] implement `ownsScore(scoreId, teacherId)`
- [ ] ensure all guards rely on **joins**, not direct ids
- [ ] centralize all guards in one file

---

## 4. repositories (data access layer)

### teachers

- [ ] findByEmail()
- [ ] findById()
- [ ] updateProfile()

### classes

- [ ] createClass()
- [ ] getClassesByTeacher()
- [ ] getClassById()
- [ ] updateClass()
- [ ] deleteClass()

### students

- [ ] createStudent()
- [ ] getStudentsByClass()
- [ ] updateStudent()
- [ ] deleteStudent()

### activity types

- [ ] createActivityType()
- [ ] getTypesByClass()
- [ ] updateActivityType()
- [ ] deleteActivityType()

### activities

- [ ] createActivity()
- [ ] getActivitiesByClass()
- [ ] updateActivity()
- [ ] deleteActivity()

### scores

- [ ] createScore()
- [ ] getScoresByActivity()
- [ ] updateScore()
- [ ] deleteScore()

---

## 5.

- [ ] ensure student belongs to class before scoring
- [ ] ensure activity belongs to same class as student
- [ ] enforce unique `(student_id, activity_id)`
- [ ] prevent negative scores
- [ ] prevent score > max_score
- [ ] validate activity type weights (optional sum = 100)

---

## 6.

- [ ] classService

  - [ ] create class
  - [ ] delete class (cascade awareness)

- [ ] studentService

  - [ ] enroll student

- [ ] gradingService

  - [ ] record score
  - [ ] update score

- [ ] reportService

  - [ ] calculate per-activity totals
  - [ ] calculate per-student averages
  - [ ] calculate final grades

---

## 7.

- [ ] authController

  - [ ] login
  - [ ] logout
  - [ ] register

- [ ] classController

  - [ ] index
  - [ ] create
  - [ ] update
  - [ ] delete

- [ ] studentController
- [ ] activityTypeController
- [ ] activityController
- [ ] scoreController

---

## 8.

- [ ] define routes in one file
- [ ] group routes under `/dashboard`
- [ ] attach `requireAuth()` middleware
- [ ] ensure all mutating routes check ownership

---

## 9.

- [ ] layout system (`_layout.html`)
- [ ] navigation highlighting for active page
- [ ] forms with csrf tokens
- [ ] error flash messages
- [ ] success flash messages
- [ ] confirm dialogs for deletes

---

## 10

- [ ] csrf protection
- [ ] session regeneration on login
- [ ] httpOnly + secure cookies
- [ ] input escaping in templates
- [ ] prepared statements everywhere

---

## 11.

- [ ] add db constraints for foreign keys
- [ ] add cascading deletes where intended
- [ ] add indexes for foreign keys
- [ ] test deleting a class deletes everything beneath

---

## 12.

- [ ] teacher cannot access another teacher’s class
- [ ] teacher cannot score foreign student
- [ ] deleting class removes all dependent rows
- [ ] invalid score is rejected
- [ ] duplicate score insertion fails

---

## 13.

- [ ] soft deletes
- [ ] audit log table
- [ ] activity timestamps
- [ ] export grades (csv)
- [ ] pagination for large classes

---

## 14.

- [ ] every write has `created_by`
- [ ] every read is scoped by ownership
- [ ] every controller calls a guard
- [ ] no table is reachable without a teacher boundary

---

## 15. Integration with Google Spreadsheet

- [ ] integrate google spreadsheet by letting the user export student grades of a class.
- [ ] ensure proper ui for integration.
