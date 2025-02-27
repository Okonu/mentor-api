<?php

namespace App\Repositories\Interfaces;

interface CourseRepositoryInterface
{
    public function all();

    public function findById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getCourseMentors($courseId);
}
