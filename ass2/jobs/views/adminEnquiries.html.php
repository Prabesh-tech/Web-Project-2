<div class="admin-enquiries-page">
    <h1>Contact Enquiries</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($enquiries)): ?>
        <div class="search-hint">No enquiries have been submitted yet.</div>
    <?php else: ?>
        <div class="enquiries-table-wrapper">
            <table class="enquiries-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Received</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enquiries as $enquiry): ?>
                        <tr>
                            <td><?= intval($enquiry['id']) ?></td>
                            <td><?= htmlspecialchars($enquiry['name']) ?></td>
                            <td><?= htmlspecialchars($enquiry['email']) ?></td>
                            <td><?= htmlspecialchars($enquiry['subject'] ?? 'No subject') ?></td>
                            <td><?= htmlspecialchars(substr($enquiry['message'], 0, 120)) ?><?= strlen($enquiry['message'] ?? '') > 120 ? '...' : '' ?></td>
                            <td><?= htmlspecialchars($enquiry['status']) ?></td>
                            <td><?= htmlspecialchars($enquiry['createdAt']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-enquiries-page {
    padding: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.admin-enquiries-page h1 {
    margin-bottom: 20px;
    color: #f8fafc;
}

.enquiries-table-wrapper {
    overflow-x: auto;
}

.enquiries-table {
    width: 100%;
    border-collapse: collapse;
    background: #0f172a;
    border: 1px solid #334155;
}

.enquiries-table th,
.enquiries-table td {
    padding: 14px 16px;
    border: 1px solid #1f2937;
    text-align: left;
    color: #e2e8f0;
}

.enquiries-table th {
    background: rgba(30, 41, 59, 0.9);
    color: #f8fafc;
}

.alert-error {
    background: rgba(248, 113, 113, 0.12);
    border: 1px solid rgba(248, 113, 113, 0.35);
    color: #fca5a5;
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.search-hint {
    padding: 18px 16px;
    background: rgba(148, 163, 184, 0.08);
    border-radius: 12px;
    color: #cbd5e1;
}
</style>
