<?php require ADMINROOT . '/frontend/views/layouts/admin_header.php'; ?>
<?php require ADMINROOT . '/frontend/views/layouts/admin_sidebar.php'; ?>

<?php
    $pending = 0;
    $multiTargetCount = 0;
    $seenTargets = [];
    foreach ($data['reports'] as $report) {
        if ($report['status'] === 'Pending') {
            $pending++;
        }
        $targetKey = $report['target_type'] . ':' . $report['target_id'];
        if (!isset($seenTargets[$targetKey]) && (int)$report['active_report_count'] > 1) {
            $multiTargetCount++;
            $seenTargets[$targetKey] = true;
        }
    }
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 font-manrope">Report Center</h1>
        <p class="text-slate-500 text-sm">Review community flags, spot repeated reports, and take moderation action.</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-100"><?php echo $pending; ?> Pending</span>
        <span class="px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-100"><?php echo $multiTargetCount; ?> Repeat Targets</span>
    </div>
</div>

<?php flash('admin_message'); ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pending Reports</p>
        <p class="mt-2 text-3xl font-black text-slate-900"><?php echo $pending; ?></p>
    </div>
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Reports</p>
        <p class="mt-2 text-3xl font-black text-slate-900"><?php echo count($data['reports']); ?></p>
    </div>
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Targets With Multiple Active Reports</p>
        <p class="mt-2 text-3xl font-black text-red-600"><?php echo $multiTargetCount; ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reporter</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reported Target</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reports</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Reason</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($data['reports'] as $report): ?>
                <?php $hasRepeatReports = (int)$report['active_report_count'] > 1; ?>
                <tr id="report-row-<?php echo $report['report_id']; ?>" class="hover:bg-slate-50/80 transition-colors <?php echo $hasRepeatReports ? 'bg-red-50/30' : ''; ?>">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($report['reporter_name']); ?></p>
                        <p class="text-[11px] text-slate-500"><?php echo htmlspecialchars($report['reporter_email']); ?></p>
                        <p class="text-[10px] text-slate-400 mt-1"><?php echo date('M d, Y', strtotime($report['created_at'])); ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="w-fit px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase"><?php echo htmlspecialchars($report['target_type']); ?> #<?php echo (int)$report['target_id']; ?></span>
                            <?php if(!empty($report['target_preview'])): ?>
                                <p class="text-[11px] text-slate-900 font-medium truncate max-w-[240px]">
                                    <?php echo htmlspecialchars(substr($report['target_preview'], 0, 70)) . (strlen($report['target_preview']) > 70 ? '...' : ''); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="w-fit px-2.5 py-1 rounded-lg text-[11px] font-black <?php echo $hasRepeatReports ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-600 border border-slate-200'; ?>">
                                <?php echo (int)$report['active_report_count']; ?> active
                            </span>
                            <span class="text-[10px] text-slate-400"><?php echo (int)$report['total_report_count']; ?> all time</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600 italic max-w-[260px] truncate">"<?php echo htmlspecialchars($report['reason']); ?>"</td>
                    <td class="px-6 py-4">
                        <?php if($report['status'] == 'Pending'): ?>
                            <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-lg border border-amber-100">Pending</span>
                        <?php elseif($report['status'] == 'Resolved'): ?>
                            <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-lg border border-green-100">Resolved</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold rounded-lg border border-slate-100">Dismissed</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="viewReportDetails(<?php echo (int)$report['report_id']; ?>)" title="Open Moderation Preview" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                            <?php if($report['status'] == 'Pending'): ?>
                                <button onclick="runReportAction(<?php echo (int)$report['report_id']; ?>, 'resolve')" title="Resolve" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                </button>
                                <button onclick="runReportAction(<?php echo (int)$report['report_id']; ?>, 'dismiss')" title="Dismiss" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                                </button>
                            <?php endif; ?>
                            <button onclick="runReportAction(<?php echo (int)$report['report_id']; ?>, 'delete')" title="Delete Report" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($data['reports'])): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm italic">No active reports to review.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="reportDetailModal" class="fixed inset-0 z-[150] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeReportModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-24px)] max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
            <div>
                <div class="flex items-center gap-2">
                    <h3 id="modal-target-title" class="text-lg font-bold text-slate-900 font-manrope">Report Details</h3>
                    <span id="modal-repeat-badge" class="hidden px-2 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-lg border border-red-200"></span>
                </div>
                <p id="modal-report-date" class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Submitted on ...</p>
            </div>
            <button onclick="closeReportModal()" class="p-2 hover:bg-white rounded-full text-slate-400 transition-colors shadow-sm">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 max-h-[72vh] overflow-y-auto">
            <div class="lg:col-span-2 p-6 space-y-5">
                <section>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Current Report</label>
                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <p id="modal-report-reason" class="text-sm text-amber-900 italic"></p>
                        <p id="modal-reporter" class="text-[11px] text-amber-700 mt-2 font-bold"></p>
                    </div>
                </section>

                <section>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Reported Content</label>
                    <div id="modal-target-content" class="p-5 bg-slate-50 rounded-xl text-sm text-slate-700 leading-relaxed whitespace-pre-wrap border border-slate-100"></div>
                </section>

                <section>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">All Reports On This Target</label>
                    <div id="modal-related-reports" class="space-y-2"></div>
                </section>
            </div>

            <aside class="p-6 bg-slate-50/60 border-l border-slate-100 space-y-5">
                <section>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Target Information</label>
                    <dl id="modal-target-meta" class="space-y-2"></dl>
                </section>

                <section>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Moderation Actions</label>
                    <div id="modal-actions" class="grid grid-cols-1 gap-2"></div>
                </section>
            </aside>
        </div>
    </div>
</div>

<script>
let currentReportDetails = null;

async function viewReportDetails(reportId) {
    const modal = document.getElementById('reportDetailModal');
    try {
        const res = await fetch(`${URLROOT}/admin/get_report_details/${reportId}`);
        const data = await res.json();
        
        if (data.success) {
            currentReportDetails = data;
            document.getElementById('modal-target-title').textContent = data.title || `${data.target_type} #${data.target_id}`;
            document.getElementById('modal-report-date').textContent = 'Submitted on ' + data.date;
            document.getElementById('modal-report-reason').textContent = `"${data.reason}"`;
            document.getElementById('modal-reporter').textContent = `Reported by ${data.reporter_name} (${data.reporter_email})`;

            const repeatBadge = document.getElementById('modal-repeat-badge');
            if (Number(data.active_report_count) > 1) {
                repeatBadge.textContent = `${data.active_report_count} active reports`;
                repeatBadge.classList.remove('hidden');
            } else {
                repeatBadge.classList.add('hidden');
            }
            
            renderTargetContent(data);
            renderTargetMeta(data.target || {});
            renderRelatedReports(data.related_reports || []);
            renderModerationActions(data);

            modal.classList.remove('hidden');
        }
    } catch(e) {
        adminInlineToast('Could not load report details.', 'error');
    }
}

function renderTargetContent(data) {
    let contentHtml = escapeHtml(data.content || 'No content found. The target may already have been removed.');
    if (data.content && data.content.includes('[Image: ')) {
        const parts = data.content.split('[Image: ');
        const imageUrl = parts[1].split(']')[0];
        contentHtml = escapeHtml(parts[0]) + `<img src="${escapeAttr(imageUrl)}" class="mt-4 rounded-xl border border-slate-200 shadow-sm max-h-[360px] w-full object-cover">`;
    }
    document.getElementById('modal-target-content').innerHTML = contentHtml;
}

function renderTargetMeta(meta) {
    const container = document.getElementById('modal-target-meta');
    const entries = Object.entries(meta);
    if (!entries.length) {
        container.innerHTML = '<p class="text-xs text-slate-500">No target metadata available.</p>';
        return;
    }

    container.innerHTML = entries.map(([key, value]) => `
        <div class="p-3 bg-white rounded-lg border border-slate-100">
            <dt class="text-[10px] font-black text-slate-400 uppercase tracking-widest">${escapeHtml(key)}</dt>
            <dd class="text-xs font-bold text-slate-700 mt-1 break-words">${escapeHtml(value || 'Not set')}</dd>
        </div>
    `).join('');
}

function renderRelatedReports(reports) {
    const container = document.getElementById('modal-related-reports');
    if (!reports.length) {
        container.innerHTML = '<p class="text-xs text-slate-500">No other reports for this target.</p>';
        return;
    }

    container.innerHTML = reports.map(report => `
        <div class="p-3 rounded-xl border ${report.status === 'Pending' ? 'border-amber-100 bg-amber-50/60' : 'border-slate-100 bg-white'}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-900">${escapeHtml(report.reporter_name)}</p>
                    <p class="text-[11px] text-slate-500">${escapeHtml(report.reporter_email)}</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-black ${report.status === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500'}">${escapeHtml(report.status)}</span>
            </div>
            <p class="mt-2 text-xs text-slate-700 italic">"${escapeHtml(report.reason)}"</p>
            <p class="mt-2 text-[10px] text-slate-400">${escapeHtml(new Date(report.created_at).toLocaleString())}</p>
        </div>
    `).join('');
}

function renderModerationActions(data) {
    const actionsDiv = document.getElementById('modal-actions');
    const targetType = encodeURIComponent(data.target_type);
    const targetId = encodeURIComponent(data.target_id);
    const reportId = encodeURIComponent(data.report_id);
    const deleteTargetButton = data.target_type === 'Post'
        ? `<button onclick="deleteReportedPost(${Number(data.target_id)})" class="py-2.5 px-3 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition-all flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">delete_forever</span> Remove Post</button>`
        : data.target_type === 'Job'
            ? `<button onclick="deleteReportedJob(${Number(data.target_id)})" class="py-2.5 px-3 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition-all flex items-center justify-center gap-2"><span class="material-symbols-outlined text-[16px]">delete_forever</span> Remove Job</button>`
            : `<a href="${URLROOT}/admin/users" class="py-2.5 px-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all text-center">Open Users</a>`;

    actionsDiv.innerHTML = `
        <a href="${escapeAttr(data.target_url)}" class="py-2.5 px-3 bg-[#0A66C2] text-white rounded-xl text-xs font-bold hover:bg-[#004182] transition-all text-center">Open Management Page</a>
        <button onclick="runReportAction(${reportId}, 'resolve')" class="py-2.5 px-3 bg-green-50 text-green-700 border border-green-100 rounded-xl text-xs font-bold hover:bg-green-100 transition-all">Resolve This Report</button>
        <button onclick="runReportAction(${reportId}, 'dismiss')" class="py-2.5 px-3 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl text-xs font-bold hover:bg-amber-100 transition-all">Dismiss This Report</button>
        <button onclick="runTargetReportsAction('${targetType}', '${targetId}', 'resolve')" class="py-2.5 px-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-bold hover:bg-emerald-100 transition-all">Resolve All Active Target Reports</button>
        <button onclick="runTargetReportsAction('${targetType}', '${targetId}', 'dismiss')" class="py-2.5 px-3 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all">Dismiss All Active Target Reports</button>
        ${deleteTargetButton}
        <button onclick="runReportAction(${reportId}, 'delete')" class="py-2.5 px-3 bg-white text-red-600 border border-red-100 rounded-xl text-xs font-bold hover:bg-red-50 transition-all">Delete This Report Record</button>
    `;
}

async function runReportAction(reportId, action) {
    const labels = { resolve: 'resolve this report', dismiss: 'dismiss this report', delete: 'delete this report record' };
    const confirmed = await pnModal({
        title: 'Confirm Action',
        message: `Do you want to ${labels[action] || 'continue'}?`,
        type: action === 'delete' ? 'warning' : 'info',
        confirmText: 'Continue',
        cancelText: 'Cancel',
        isDanger: action === 'delete'
    });
    if (!confirmed) return;

    try {
        const res = await fetch(`${URLROOT}/admin/report_action/${reportId}/${action}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            adminInlineToast('Report updated.', 'success');
            setTimeout(() => location.reload(), 450);
        } else {
            adminInlineToast(data.message || 'Action failed.', 'error');
        }
    } catch(e) {
        adminInlineToast('Server error.', 'error');
    }
}

async function runTargetReportsAction(targetType, targetId, action) {
    const confirmed = await pnModal({
        title: 'Bulk Report Action',
        message: `This will ${action} all active reports for this target. The target itself will remain unchanged.`,
        type: 'info',
        confirmText: 'Apply To All',
        cancelText: 'Cancel'
    });
    if (!confirmed) return;

    try {
        const res = await fetch(`${URLROOT}/admin/target_reports_action/${targetType}/${targetId}/${action}`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            adminInlineToast('Target reports updated.', 'success');
            setTimeout(() => location.reload(), 450);
        } else {
            adminInlineToast(data.message || 'Action failed.', 'error');
        }
    } catch(e) {
        adminInlineToast('Server error.', 'error');
    }
}

async function deleteReportedPost(postId) {
    const confirmed = await pnModal({
        title: 'Remove Reported Post',
        message: 'This removes the reported post from the platform. The related reports will remain for audit history.',
        type: 'warning',
        confirmText: 'Remove Post',
        cancelText: 'Cancel',
        isDanger: true
    });
    if (!confirmed) return;
    const res = await fetch(`${URLROOT}/admin/delete_post/${postId}`, { method: 'POST' });
    const data = await res.json();
    adminInlineToast(data.success ? 'Post removed.' : 'Could not remove post.', data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 500);
}

async function deleteReportedJob(jobId) {
    const confirmed = await pnModal({
        title: 'Remove Reported Job',
        message: 'This removes the reported job listing from the platform. The related reports will remain for audit history.',
        type: 'warning',
        confirmText: 'Remove Job',
        cancelText: 'Cancel',
        isDanger: true
    });
    if (!confirmed) return;
    const res = await fetch(`${URLROOT}/admin/delete_job/${jobId}`, { method: 'POST' });
    const data = await res.json();
    adminInlineToast(data.success ? 'Job removed.' : 'Could not remove job.', data.success ? 'success' : 'error');
    if (data.success) setTimeout(() => location.reload(), 500);
}

function closeReportModal() {
    document.getElementById('reportDetailModal').classList.add('hidden');
}

function adminInlineToast(msg, type = 'info') {
    const existing = document.getElementById('admin-inline-toast');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'admin-inline-toast';
    const bg = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-blue-600';
    t.className = `fixed bottom-6 right-6 z-[9999] px-5 py-3 rounded-xl shadow-lg text-sm font-bold ${bg} text-white transition-all duration-300`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3500);
}

function escapeHtml(value) {
    return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function escapeAttr(value) {
    return escapeHtml(value).replace(/`/g, '&#96;');
}
</script>

<?php require ADMINROOT . '/frontend/views/layouts/admin_footer.php'; ?>
