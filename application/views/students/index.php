<h2>Students List</h2>

<a href="<?php echo site_url('students/create'); ?>">Add Student</a>

<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo $student->id ?></td>
            <td><?php echo $student->name ?></td>
            <td><?php echo $student->email ?></td>
            <td><?php echo $student->course ?></td>
            <td><a href="<?php echo site_url('students/edit/' . $student->id); ?>">Edit</a></td>
            <td><a href="<?php echo site_url('students/delete/' . $student->id); ?>" onclick="return confirm('Delete?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php if ($this->session->flashdata('message')): ?>
    <p style="color: green; font-weight: bold;">
        <?= $this->session->flashdata('message'); ?>
    </p>
<?php endif; ?>