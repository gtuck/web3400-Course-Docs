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

        // Posts - use model methods
        $totalPosts = Post::count();
        $draftPosts = Post::countByStatus('draft');
        $publishedPosts = Post::countByStatus('published');
        $featuredPosts = Post::countFeatured();

        // Users - use model methods
        $totalUsers = User::countByRole('user');
        $totalAdmins = User::countByRole('admin');

        // Contact messages
        $totalContacts = Contact::count();

        // Engagement aggregates - use model methods
        $avgLikes = Post::averageLikes();
        $avgFavs = Post::averageFavs();
        $avgComments = Post::averageComments();

        // Complex analytics query (dashboard-specific, OK to keep in controller)
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
        return User::all(limit: $limit, orderBy: 'id DESC');
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
                u.name AS name,
                COUNT(*) AS interactions
            FROM users u
            JOIN (
                SELECT user_id FROM post_likes
                UNION ALL
                SELECT user_id FROM post_favorites
                UNION ALL
                SELECT user_id FROM comments
            ) ui ON ui.user_id = u.id
            GROUP BY u.id, u.name
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
