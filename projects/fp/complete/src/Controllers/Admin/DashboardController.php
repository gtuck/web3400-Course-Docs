<?php
namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Contact;
use App\Support\Database;
use PDO;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
    }

    public function index(): void
    {
        $kpis = $this->buildKpis();
        $recentContacts = $this->recentContacts();
        $recentUsers = $this->recentUsers();
        $recentPosts = $this->recentPosts();

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'kpis' => $kpis,
            'recentContacts' => $recentContacts,
            'recentUsers' => $recentUsers,
            'recentPosts' => $recentPosts,
        ]);
    }

    private function buildKpis(): array
    {
        $pdo = Database::pdo();

        // Posts
        $totalPosts = Post::count();
        $draftPosts = Post::countByStatus('draft');
        $publishedPosts = Post::countByStatus('published');
        $featuredPosts = (int)$pdo->query("SELECT COUNT(*) FROM `posts` WHERE `is_featured` = 1")->fetchColumn();

        // Users
        $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'user'")->fetchColumn();
        $totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'admin'")->fetchColumn();

        // Contact messages
        $totalContacts = Contact::count();

        // Engagement aggregates
        $avgLikes = (float)$pdo->query("SELECT COALESCE(ROUND(AVG(likes), 2), 0) FROM `posts`")->fetchColumn();
        $avgFavs = (float)$pdo->query("SELECT COALESCE(ROUND(AVG(favs), 2), 0) FROM `posts`")->fetchColumn();
        $avgComments = (float)$pdo->query("SELECT COALESCE(ROUND(AVG(comments_count), 2), 0) FROM `posts`")->fetchColumn();

        $totalInteractions = (int)$pdo->query("
            SELECT 
                (SELECT COUNT(*) FROM post_likes) +
                (SELECT COUNT(*) FROM post_favorites) +
                (SELECT COUNT(*) FROM comments)
        ")->fetchColumn();

        $mostActiveUser = $this->mostActiveUser();

        return [
            'total_posts' => $totalPosts,
            'draft_posts' => $draftPosts,
            'published_posts' => $publishedPosts,
            'featured_posts' => $featuredPosts,
            'total_users' => $totalUsers,
            'total_admins' => $totalAdmins,
            'total_contacts' => $totalContacts,
            'average_likes_per_post' => $avgLikes,
            'average_favs_per_post' => $avgFavs,
            'average_comments_per_post' => $avgComments,
            'total_interactions' => $totalInteractions,
            'most_active_user' => $mostActiveUser,
        ];
    }

    private function recentContacts(int $limit = 5): array
    {
        return Contact::all(limit: $limit, orderBy: '`id` DESC');
    }

    private function recentUsers(int $limit = 5): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT * FROM `users` ORDER BY `id` DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function recentPosts(int $limit = 5): array
    {
        return Post::all(limit: $limit, orderBy: '`published_at` DESC, `id` DESC');
    }

    private function mostActiveUser(): ?string
    {
        $pdo = Database::pdo();

        $sql = "
            SELECT 
                u.full_name AS name,
                COUNT(*) AS interactions
            FROM users u
            JOIN (
                SELECT user_id FROM post_likes
                UNION ALL
                SELECT user_id FROM post_favorites
                UNION ALL
                SELECT user_id FROM comments
            ) ui ON ui.user_id = u.id
            GROUP BY u.id, u.full_name
            ORDER BY interactions DESC
            LIMIT 1
        ";

        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $row['name'] . ' (' . $row['interactions'] . ' interactions)';
    }
}
