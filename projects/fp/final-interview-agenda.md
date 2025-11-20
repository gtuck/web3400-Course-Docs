### **Mandatory Final Project Interview Agenda**

#### **Introduction**

To verify your Final Project readiness, we’ll conduct a focused five-minute review in my **Office Hours Zoom Room**: [https://weber.zoom.us/j/8013088825](https://weber.zoom.us/j/8013088825).

Please schedule your appointment via my **Google Appointment Schedule**: [https://calendar.app.google/kArQ1voiZ8yUc5dY8](https://calendar.app.google/kArQ1voiZ8yUc5dY8)

Each meeting is strictly five minutes for a quick walkthrough. If issues arise, continue to the next agenda item; deeper help should be booked separately during regular office hours.

---

### **Checklist for Review (Final Project Requirements)**

**New Dashboard Features**
- [ ] `/admin/dashboard` is routed through the Router to `Admin\DashboardController`
- [ ] Dashboard restricted to authenticated admin users; admins land here after login
- [ ] Post model analytics helpers: `count()`, `countByStatus()`, `countFeatured()`, `averageLikes()`, `averageFavs()`, `averageComments()` (averages scoped to published)
- [ ] User model counting helper: `User::countByRole()`
- [ ] BaseModel exposes generic `count()` for totals
- [ ] DashboardController uses model methods for KPIs; only complex analytics (e.g., totalInteractions, mostActiveUser) may use raw SQL
- [ ] Dashboard view renders KPI cards and recent activity (contact messages and/or posts/users) via layout/partials
- [ ] Navigation shows an admin dashboard link (admins only); footer includes site info and contact link

**Code Quality & Security**
- [ ] Thin controllers, fat models; consistent with Project 08 patterns
- [ ] All dynamic output escaped with `$this->e()`
- [ ] All forms include CSRF tokens
- [ ] Prepared statements for parameterized queries

---

### **Quick Demo Flow (5 minutes)**
1. Login as admin → confirm redirect to `/admin/dashboard`
2. Show KPI cards updating from live data (posts/users/contacts/engagement averages)
3. Show recent activity tables (contacts/posts/users) and safe date handling
4. Demonstrate access control (non-admin blocked)
5. Point out code locations for model helpers and DashboardController queries

---

### **Outcome**
- Approved: Demonstrated implementation and understanding of all checklist items.
- Needs Revision: Specific gaps identified; schedule follow-up after fixes.
