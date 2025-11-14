<?php

namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Support\Database;

class CommentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin', 'editor');
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? null;

        $sql = "
            SELECT c.*, p.title AS post_title, p.slug AS post_slug, u.name AS user_name, u.email AS user_email
            FROM `comments` c
            JOIN `posts` p ON p.id = c.post_id
            JOIN `users` u ON u.id = c.user_id
        ";
        $params = [];
        if ($status && in_array($status, ['pending', 'published', 'deleted'], true)) {
            $sql .= " WHERE c.status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY c.created_at DESC LIMIT 200";

        $pdo = Database::pdo();
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $comments = $stmt->fetchAll();

        $this->render('admin/comments/index', [
            'title' => 'Manage Comments',
            'comments' => $comments,
            'status' => $status,
        ]);
    }

    public function publish(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/admin/comments');
        }

        $comment = Comment::find($id);
        if ($comment && $comment['status'] !== 'published') {
            Comment::update($id, ['status' => 'published']);
            Post::incrementComments((int)$comment['post_id']);
        }

        $this->redirect('/admin/comments');
    }

    public function destroy(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Invalid CSRF token.', 'is-danger');
            $this->redirect('/admin/comments');
        }

        $comment = Comment::find($id);
        if ($comment && $comment['status'] !== 'deleted') {
            Comment::update($id, ['status' => 'deleted']);
            Post::decrementComments((int)$comment['post_id']);
        }

        $this->redirect('/admin/comments');
    }
}
