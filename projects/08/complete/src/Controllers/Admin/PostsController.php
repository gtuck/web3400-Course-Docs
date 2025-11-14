<?php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Post;
use App\Support\Validator;

class PostsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin', 'editor');
    }

    public function index(): void
    {
        $posts = Post::all(limit: 200, orderBy: '`published_at` DESC, `id` DESC');
        $this->render('admin/posts/index', [
            'title' => 'Manage Posts',
            'posts' => $posts,
        ]);
    }

    public function create(): void
    {
        $this->render('admin/posts/create', [
            'title' => 'Create Post',
        ]);
    }

    public function store(): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/posts/create');
        }

        $user = $this->user();
        $data = [
            'author_id' => (int)($user['id'] ?? 0),
            'title' => trim($_POST['title'] ?? ''),
            'slug' => $this->slugify($_POST['slug'] ?? ($_POST['title'] ?? '')),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'featured_image' => trim($_POST['featured_image'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];

        $errors = Validator::validate($data, [
            'title' => 'required|max:255',
            'slug' => 'required|max:100',
            'status' => 'required|in:draft,published,archived,deleted',
        ]);
        if (Post::existsBy('slug', $data['slug'])) {
            $errors['slug'][] = 'Slug must be unique.';
        }
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->render('admin/posts/create', ['title' => 'Create Post', 'old' => $data]);
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $id = Post::create($data);
        $this->flash('Post created.', 'is-success');
        $this->redirect('/admin/posts');
    }

    public function edit(int $id): void
    {
        $post = Post::find($id);
        if (!$post) {
            $this->flash('Post not found.', 'is-warning');
            $this->redirect('/admin/posts');
        }
        $this->render('admin/posts/edit', [
            'title' => 'Edit Post',
            'post' => $post,
        ]);
    }

    public function update(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect("/admin/posts/{$id}/edit");
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => $this->slugify($_POST['slug'] ?? ($_POST['title'] ?? '')),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'featured_image' => trim($_POST['featured_image'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];

        $errors = Validator::validate($data, [
            'title' => 'required|max:255',
            'slug' => 'required|max:100',
            'status' => 'required|in:draft,published,archived,deleted',
        ]);
        if (Post::existsBy('slug', $data['slug'], $id)) {
            $errors['slug'][] = 'Slug must be unique.';
        }
        if (!empty($errors)) {
            foreach (Validator::flattenErrors($errors) as $m) {
                $this->flash($m, 'is-warning');
            }
            $this->redirect("/admin/posts/{$id}/edit");
        }

        // published_at handling
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        } else {
            $data['published_at'] = null;
        }

        Post::update($id, $data);
        $this->flash('Post updated.', 'is-success');
        $this->redirect('/admin/posts');
    }

    public function publish(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/posts');
        }
        Post::update($id, ['status' => 'published', 'published_at' => date('Y-m-d H:i:s')]);
        $this->flash('Post published.', 'is-success');
        $this->redirect('/admin/posts');
    }

    public function unpublish(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/posts');
        }
        Post::update($id, ['status' => 'draft', 'published_at' => null]);
        $this->flash('Post set to draft.', 'is-info');
        $this->redirect('/admin/posts');
    }

    public function destroy(int $id): void
    {
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed.', 'is-danger');
            $this->redirect('/admin/posts');
        }
        // Soft delete via status field
        Post::update($id, ['status' => 'deleted']);
        $this->flash('Post deleted.', 'is-warning');
        $this->redirect('/admin/posts');
    }

    // slugify provided by base Controller
}
