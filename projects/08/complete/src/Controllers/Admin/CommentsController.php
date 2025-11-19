<?php

namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Comment;
use App\Models\Post;

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

        $comments = Comment::allWithDetails($status);

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
            if ($comment['status'] === 'published') {
                Post::decrementComments((int)$comment['post_id']);
            }
        }

        $this->redirect('/admin/comments');
    }
}
