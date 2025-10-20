
const form = document.getElementById('msgForm');
const input = document.getElementById('message');
const chat = document.getElementById('chat');

function appendMsg(text, who = 'bot') {
  const d = document.createElement('div');
  d.className = 'msg ' + (who === 'user' ? 'user' : 'bot');

  //style formatting
  text = text
 
    .replace(/^##\s*(.*)$/gm, '<br><strong style="font-size:1.1em;">$1</strong><br>')

    .replace(/---+/g, '<hr style="border:none;border-top:1px solid #ccc;margin:6px 0;">')

    .replace(/\*\*(.*?)\*\*/g, '<br><b>$1</b><br>')

    .replace(/(^|[^*])\*(?!\*)(.*?)\*(?!\*)([^*]|$)/g, '$1<i>$2</i>$3')

    .replace(/\n/g, '<br>');

  d.innerHTML = text;

  chat.appendChild(d);
  chat.scrollTop = chat.scrollHeight;
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const text = input.value.trim();
  if (!text) return;

  appendMsg(text, 'user');
  input.value = '';

  //
  const thinking = document.createElement('div');
  thinking.className = 'msg bot';
  thinking.textContent = '…';
  chat.appendChild(thinking);
  chat.scrollTop = chat.scrollHeight;

  try {
    const res = await fetch('chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text })
    });

    const data = await res.json();
    thinking.remove();

    if (res.ok && data && data.reply) {
      appendMsg(data.reply, 'bot');
    } else {
      appendMsg('Error: ' + (data.error || 'No reply'), 'bot');
    }
  } catch (err) {
    thinking.remove();
    appendMsg('Network error: ' + err.message, 'bot');
  }
});

// Toggle chatbot visibility
const chatToggle = document.getElementById('chatToggle');
const chatWrapper = document.getElementById('chatWrapper');

chatToggle.addEventListener('click', () => {
  chatWrapper.classList.toggle('hidden');
});
