<h2>Update Student</h2>

<form method="post">
    <input type="text" name="name" placeholder="Name" value="<?= $student->name; ?>"><br><br>
    <input type="text" name="email" placeholder="Email" value="<?= $student->email; ?>"><br><br>
    <input type="text" name="course" placeholder="Course" value="<?= $student->course; ?>"><br><br>
    <button type="submit">Update</button>
</form>