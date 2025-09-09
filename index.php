<?php 
session_start();
require("db.php");
include("header.php");
$featured_job_query = "SELECT * FROM jobs 
                WHERE type = 'Full-time' 
                AND deadline > NOW() 
                ORDER BY posted_at DESC 
                LIMIT 4";

$featured_result = mysqli_query($conn, $featured_job_query);
$featured_jobs = mysqli_fetch_all($featured_result, MYSQLI_ASSOC);

?>

<div class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Find Your Dream Job</h1>
        <p class="lead">Join thousands of companies and candidates using our platform to connect</p>
        <div class="mt-4">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php?type=employee" class="btn btn-light btn-lg me-2">I'm Job Seeker</a>
                <a href="register.php?type=employer" class="btn btn-outline-light btn-lg">I'm Employer</a>
            <?php else: ?>
                <a href="<?= $_SESSION['user_type'] == 'employee' ? 'employee/dashboard.php' : 'employer/dashboard.php' ?>" class="btn btn-light btn-lg">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="row mb-5">
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-search"></i>
            </div>
            <h3>Find Jobs</h3>
            <p>Browse through hundreds of job listings from top companies in your field.</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <h3>Build Your CV</h3>
            <p>Create a professional resume with our easy-to-use resume builder tool.</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-briefcase"></i>
            </div>
            <h3>Hire Talent</h3>
            <p>Post your job openings and find qualified candidates for your company.</p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Featured Jobs</h2>
            <div class="row">
                <?php foreach ($featured_jobs as $job): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 card-hover">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($job['company_name']) ?></h6>
                                <p class="card-text"><?= substr(htmlspecialchars($job['description']), 0, 100) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary"><?= ucfirst(str_replace('-', ' ', $job['type'])) ?></span>
                                    <a href="/job-portal2/employee/view_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-3">
                <a href="/job-portal2/employee/jobs.php" class="btn btn-primary">View All Jobs</a>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">For Job Seekers</h3>
                    <p class="card-text">Create your profile, build a professional resume, and apply to jobs with just a few clicks.</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Easy resume builder</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Job application tracking</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Personalized job recommendations</li>
                    </ul>
                    
               





                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">For Employers</h3>
                    <p class="card-text">Post your job openings, review applications, and find the perfect candidate for your team.</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Easy job posting</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Applicant management</li>
                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i> Access to qualified candidates</li>
                    </ul>
                   
                    
                </div>
            </div>
        </div>
    </div>
</div>

<button id="chatToggle" class="chat-toggle" aria-label="Open chat">💬</button>

<div id="chatPanel" class="chat-panel hidden" role="dialog" aria-modal="false" aria-label="Assistant chat">
  <div class="chat-header">
    <h4>CV Assistant</h4>
    <button id="chatClose" class="close-btn" aria-label="Close chat">✕</button>
  </div>

  <div class="chat-body" id="chatBody" aria-live="polite">
    <!-- messages go here -->
  </div>

  <div class="chat-suggestions" id="suggestions">
    <!-- suggestion chips -->
  </div>

  <div class="chat-input-area">
    <input id="chatInput" class="chat-input" placeholder="Ask something like: 'How to download CV?'" aria-label="Chat message">
    <button id="chatSend" class="chat-send">Send</button>
  </div>
</div>


<?php include 'footer.php'; ?>

?>
<style>
  /* Chat widget styles */
  .chat-toggle {
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg,#4f8cff,#0056b3);
    color: white;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow: 0 10px 30px rgba(79,140,255,0.25);
    cursor: pointer;
    z-index: 9999;
    border: none;
  }
  .chat-panel {
    position: fixed;
    right: 20px;
    bottom: 90px;
    width: 360px;
    max-height: 68vh;
    background: rgba(255,255,255,0.98);
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.18);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    z-index: 9999;
    transform: translateY(10px);
    transition: transform .18s ease, opacity .18s ease;
  }
  body.dark .chat-panel { background: rgba(20,22,26,0.96); color: #eaeef8; }
  .chat-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 14px; border-bottom:1px solid rgba(0,0,0,0.06);
    background: linear-gradient(90deg, rgba(79,140,255,0.06), transparent);
  }
  body.dark .chat-header { border-bottom:1px solid rgba(255,255,255,0.03); }
  .chat-header h4 { margin:0; font-family: 'Poppins', sans-serif; color:#154bff; font-size:15px; }
  body.dark .chat-header h4 { color: #79aefc; }
  .chat-header .close-btn { background:transparent; border:none; cursor:pointer; font-size:16px; }
  .chat-body { padding:12px; overflow:auto; flex:1; display:flex; flex-direction:column; gap:8px; }
  .msg { max-width: 80%; padding:10px 12px; border-radius:10px; font-size:14px; line-height:1.45; }
  .msg.user { align-self: flex-end; background: linear-gradient(135deg,#e9f0ff,#cfe0ff); color:#04204a; border-bottom-right-radius:4px; }
  body.dark .msg.user { background: linear-gradient(135deg,#2b3442,#232831); color:#dfe9ff; }
  .msg.bot { align-self:flex-start; background: #f6f8fb; color:#222; border-bottom-left-radius:4px; }
  body.dark .msg.bot { background: rgba(255,255,255,0.03); color:#dbe8ff; }
  .chat-suggestions { display:flex; gap:8px; flex-wrap:wrap; padding:8px 12px; border-top:1px dashed rgba(0,0,0,0.04); }
  body.dark .chat-suggestions { border-top:1px dashed rgba(255,255,255,0.02); }
  .chip { background: rgba(0,0,0,0.06); padding:6px 10px; border-radius:999px; font-size:13px; cursor:pointer; }
  body.dark .chip { background: rgba(255,255,255,0.04); }
  .chat-input-area { display:flex; gap:8px; padding:10px; border-top:1px solid rgba(0,0,0,0.04); }
  body.dark .chat-input-area { border-top:1px solid rgba(255,255,255,0.03); }
  .chat-input { flex:1; padding:10px; border-radius:8px; border:1px solid rgba(0,0,0,0.06); background:transparent; font-size:14px; }
  body.dark .chat-input { border:1px solid rgba(255,255,255,0.04); color:inherit; }
  .chat-send { background: linear-gradient(135deg,#4f8cff,#0056b3); color:#fff; border:none; padding:10px 12px; border-radius:8px; cursor:pointer; }
  .hidden { display:none; }
  /* small animations */
  .chat-panel.enter { transform: translateY(0); opacity:1; }
  .chat-panel.leave { transform: translateY(10px); opacity:0; }
  .muted { color: rgba(0,0,0,0.45); font-size:12px; }
  body.dark .muted { color: rgba(255,255,255,0.45); }
</style>

<script> 
const fixedAnswers = [
  { keys: ['how to download', 'download cv', 'export pdf'], answer: 'To download your CV click "Download PDF". If you want better print quality, ensure you use the light theme or enable high-resolution export in settings.' },
  { keys: ['how to edit', 'edit cv', 'make changes'], answer: 'Open the editor on the left, make changes in the fields (or upload a new profile image), then click "Save" to keep changes.' },
  { keys: ['dark mode', 'theme', 'toggle theme'], answer: 'Use the Theme button in the editor (or top bar) to toggle dark/light theme. The app remembers your preference.' },
  { keys: ['profile image', 'upload picture', 'change photo'], answer: 'Click the "Profile image" upload field in the editor panel and select a JPG/PNG file. The image will be previewed and saved.' },
  { keys: ['multiple pages', 'page break', 'top margin'], answer: 'If the PDF shows extra top space, make sure the document container has no top margin and @page margins are small. We already set minimal margins for exports.' },
  { keys: ['reset', 'clear', 'delete'], answer: 'Click the "Clear" button in the editor to remove saved CV content from your browser (localStorage).' },
  { keys: ['help', 'support'], answer: 'I can answer common questions about using this CV builder. Try clicking one of the suggested questions or type your question.' }
];

// suggested questions shown as chips
const suggestionsList = [
  'How to download?',
  'How to edit my CV?',
  'How to change profile image?',
  'Dark mode / Theme',
  'Why top margin in PDF?'
];

const chatToggle = document.getElementById('chatToggle');
const chatPanel = document.getElementById('chatPanel');
const chatClose = document.getElementById('chatClose');
const chatBody = document.getElementById('chatBody');
const suggestions = document.getElementById('suggestions');
const chatInput = document.getElementById('chatInput');
const chatSend = document.getElementById('chatSend');

let chatOpen = false;

/* Utilities */
function saveChatState() {
  const msgs = Array.from(chatBody.querySelectorAll('.msg')).map(el => ({role: el.dataset.role, text: el.textContent}));
  localStorage.setItem('cvChatHistory', JSON.stringify(msgs));
}
function loadChatState() {
  const raw = localStorage.getItem('cvChatHistory');
  if (!raw) return;
  try {
    const msgs = JSON.parse(raw);
    msgs.forEach(m => appendMessage(m.role, m.text, false));
  } catch(e) { console.warn('chat history corrupted') }
}
function appendMessage(role, text, save=true) {
  const div = document.createElement('div');
  div.className = 'msg ' + (role === 'user' ? 'user' : 'bot');
  div.dataset.role = role;
  div.textContent = text;
  chatBody.appendChild(div);
  chatBody.scrollTop = chatBody.scrollHeight;
  if (save) saveChatState();
}

/* Matching logic: exact phrase -> keyword -> fallback */
function findFixedAnswer(input) {
  input = (input || '').toLowerCase().trim();
  if (!input) return null;

  // exact key match (if user typed identical suggestion)
  for (const item of fixedAnswers) {
    for (const k of item.keys) {
      if (input === k) return item.answer;Get 
    }
  }

  // keyword inclusion match
  for (const item of fixedAnswers) {
    for (const k of item.keys) {
      if (input.includes(k)) return item.answer;
    }
  }

  // fuzzy: look for any important token match
  const tokens = input.split(/\W+/).filter(Boolean);
  for (const item of fixedAnswers) {
    for (const k of item.keys) {
      const kTokens = k.split(/\W+/).filter(Boolean);
      if (kTokens.some(t => tokens.includes(t))) return item.answer;
    }
  }

  return null;
}

/* When user sends a message */
function handleSend(text) {
  const userText = text.trim();
  if (!userText) return;
  appendMessage('user', userText);

  // find answer
  const ans = findFixedAnswer(userText);
  if (ans) {
    setTimeout(()=> appendMessage('bot', ans), 300 + Math.random()*400);
  } else {
    // fallback: polite guide + suggestions
    setTimeout(()=> appendMessage('bot', "Sorry, I don't have an automated answer for that. Try one of the suggested questions or contact support."), 400);
  }
}

/* Render suggestion chips */
function renderSuggestions() {
  suggestions.innerHTML = '';
  suggestionsList.forEach(s => {
    const el = document.createElement('button');
    el.type = 'button';
    el.className = 'chip';
    el.textContent = s;
    el.addEventListener('click', () => {
      chatInput.value = s;
      handleSend(s);
    });
    suggestions.appendChild(el);
  });
}

/* Toggle open/close */
function openChat() {
  chatPanel.classList.remove('hidden');
  chatPanel.classList.add('enter');
  chatOpen = true;
  chatInput.focus();
}
function closeChat() {
  chatPanel.classList.remove('enter');
  chatPanel.classList.add('leave');
  setTimeout(()=> {
    chatPanel.classList.add('hidden');
    chatPanel.classList.remove('leave');
  }, 180);
  chatOpen = false;
}

/* Events */
chatToggle.addEventListener('click', ()=> {
  if (chatOpen) { closeChat(); } else { openChat(); }
});
chatClose.addEventListener('click', closeChat);
chatSend.addEventListener('click', ()=> { handleSend(chatInput.value); chatInput.value=''; chatInput.focus(); });
chatInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    handleSend(chatInput.value);
    chatInput.value = '';
  }
});

/* init */
renderSuggestions();
loadChatState();

/* EXPOSE a small API so developer can update fixedAnswers dynamically if needed */
window.CVChat = {
  addFaq: (keys, answer) => fixedAnswers.push({keys:Array.isArray(keys)?keys:[keys], answer}),
  clearHistory: () => { localStorage.removeItem('cvChatHistory'); chatBody.innerHTML=''; }
};
</script>
<!-- END: Simple AI Chatbot (rule-based) -->