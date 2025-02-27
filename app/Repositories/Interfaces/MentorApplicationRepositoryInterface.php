<?php

namespace App\Repositories\Interfaces;

interface MentorApplicationRepositoryInterface
{
    public function all();

    public function findById($id);

    public function findByUserAndStatus($userId, $status);

    public function findPendingByUser($userId);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function approve($id);

    public function reject($id, $reason = null);
}
