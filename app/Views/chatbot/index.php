<div class="bg-[#0b0f19] min-h-screen py-10 lg:py-16 text-gray-300">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

		<!-- Page Header -->
		<div class="text-center mb-12">
			<h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">Trợ lý ảo Sportzone Chatbot</h1>
			<p class="mt-3 max-w-2xl mx-auto text-lg text-gray-400 sm:mt-4">Giải đáp thắc mắc và cung cấp thông tin về thể
				thao và luyện tập.</p>
		</div>

		<!-- Chatbot Interface -->
		<div class="bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-800 top-24">

			<!-- Chatbot header -->
			<header class="flex items-center mb-6 text-white">
				<div class="w-12 h-12 bg-[#ff6600] rounded-full border border-gray-700 flex items-center justify-center">
					<i class="fa-solid fa-robot text-2xl"></i>
				</div>
				<div class="ml-4">
					<h2 class="text-2xl font-semibold text-white">Sportzone Chatbot</h2>
				</div>
			</header>

			<!-- Chatbot message UI -->
			<div class="grid xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,0.7fr)] gap-6 items-start">
				<div class="space-y-5 flex flex-col h-[46rem]">
					<div id="chatbot-message-list"
						class="flex-1 min-h-0 overflow-y-auto space-y-4 pr-1 bg-gray-900/80 border border-gray-800 rounded-2xl p-5">
						<?php if (!empty($chatHistory)): ?>
							<?php foreach ($chatHistory as $message): ?>
								<?php
								$isUser = strtolower($message['sender'] ?? '') !== 'bot';
								$text = htmlspecialchars($message['message_text'] ?? '');
								$createdAt = !empty($message['created_at']) ? date('H:i', strtotime($message['created_at'])) : '';
								?>
								<div class="flex <?= $isUser ? 'justify-end' : 'justify-start' ?>">
									<div class="max-w-[88%] flex gap-3 <?= $isUser ? 'flex-row-reverse' : 'flex-row' ?>">
										<div
											class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 <?= $isUser ? 'bg-white/10 text-white' : 'bg-gradient-to-br from-[#ff6600] to-[#ff9a4d] text-white' ?>">
											<i class="fa-solid <?= $isUser ? 'fa-user' : 'fa-robot' ?> text-sm"></i>
										</div>
										<div class="space-y-1 <?= $isUser ? 'text-right' : 'text-left' ?>">
											<div
												class="inline-block rounded-[1.5rem] px-4 py-3 border <?= $isUser ? 'border-[#ff6600]/25 bg-[#ff6600]/15 text-white' : 'border-white/10 bg-white/5 text-gray-200' ?>">
												<p class="whitespace-pre-line leading-relaxed text-sm"><?= $text ?></p>
											</div>
											<?php if ($createdAt): ?>
												<div class="text-[11px] uppercase tracking-[0.2em] text-gray-500 px-1">
													<?= htmlspecialchars($createdAt) ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<div class="rounded-2xl border border-dashed border-white/10 bg-white/5 p-8 text-center">
								<div
									class="mx-auto w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-[#ff8f3d] mb-3">
									<i class="fa-solid fa-message"></i>
								</div>
								<h3 class="text-white font-bold">Bắt đầu cuộc trò chuyện</h3>
								<p class="text-gray-400 text-sm mt-2">Lịch sử tin nhắn sẽ xuất hiện ở đây khi bạn đã trao đổi với chatbot.
								</p>
							</div>
						<?php endif; ?>
					</div>

					<form id="chatbot-form" method="POST" action="<?= BASE_URL ?>/index.php?route=chatbot"
						class="shrink-0 rounded-2xl border border-white/10 bg-white/5 p-4 sm:p-5">
						<div class="flex flex-col sm:flex-row gap-3">
							<div class="flex-1 relative">
								<i class="fa-regular fa-face-smile absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
								<input id="chatbot-input" type="text" name="message_text" required autocomplete="off"
									placeholder="Nhập câu hỏi cho Sportzone Chatbot..."
									class="w-full rounded-2xl border border-white/10 bg-[#090d16] pl-11 pr-4 py-4 text-sm text-white placeholder:text-gray-600 outline-none focus:ring-2 focus:ring-[#ff6600]/50 focus:border-[#ff6600]/50">
							</div>
							<button id="chatbot-send-btn" type="submit"
								class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#ff6600] to-[#ff9a4d] px-6 py-4 font-bold text-white shadow-lg shadow-[#ff6600]/20">
								<i class="fa-solid fa-paper-plane"></i>
								Gửi tin nhắn
							</button>
						</div>
					</form>
				</div>

				<aside class="space-y-5">
					<div class="rounded-2xl border border-[#ff6600]/20 bg-gradient-to-r from-[#ff6600]/10 to-transparent p-5">
						<div class="flex items-center gap-3 mb-4">
							<div class="w-10 h-10 rounded-2xl bg-[#ff6600]/15 text-[#ff8f3d] flex items-center justify-center">
								<i class="fa-solid fa-lightbulb"></i>
							</div>
							<div>
								<h3 class="text-white font-bold">Gợi ý nhanh</h3>
								<p class="text-sm text-gray-400">Chọn một câu hỏi mẫu để bắt đầu.</p>
							</div>
						</div>
						<div class="flex flex-wrap gap-3">
							<span class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-gray-200">Chính sách
								đổi trả như thế nào?</span>
							<span class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-gray-200">Có giao hàng
								trong ngày không?</span>
							<span class="px-4 py-2 rounded-full border border-white/10 bg-white/5 text-sm text-gray-200">Làm sao để
								chọn size phù hợp?</span>
						</div>
					</div>
					<div class="bg-gray-900/80 border border-gray-800 rounded-2xl p-5">
						<div class="text-[11px] font-black uppercase tracking-[0.22em] text-[#ff8f3d] mb-2">Tips</div>
						<h3 class="text-lg font-bold text-white mb-3">Mẹo hỏi chatbot</h3>
						<ul class="space-y-2 text-sm text-gray-400">
							<li class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-0.5"></i><span>Hỏi ngắn, rõ
									ràng.</span></li>
							<li class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-0.5"></i><span>Ưu tiên từ khóa như
									tên sản phẩm hoặc size.</span></li>
							<li class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-0.5"></i><span>Chọn gợi ý nhanh nếu
									muốn bắt đầu ngay.</span></li>
						</ul>
					</div>
				</aside>
			</div>
		</div>

		<script>
			(function () {
				const form = document.getElementById('chatbot-form');
				const input = document.getElementById('chatbot-input');
				const sendBtn = document.getElementById('chatbot-send-btn');
				const messageList = document.getElementById('chatbot-message-list');

				if (!form || !input || !sendBtn || !messageList) {
					return;
				}

				const escapeHtml = (value) => String(value)
					.replace(/&/g, '&amp;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#039;');

				const scrollToBottom = () => {
					messageList.scrollTop = messageList.scrollHeight;
				};

				const appendMessage = (text, isUser) => {
					const wrapper = document.createElement('div');
					wrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;

					wrapper.innerHTML = `
					<div class="max-w-[88%] flex gap-3 ${isUser ? 'flex-row-reverse' : 'flex-row'}">
						<div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 ${isUser ? 'bg-white/10 text-white' : 'bg-gradient-to-br from-[#ff6600] to-[#ff9a4d] text-white'}">
							<i class="fa-solid ${isUser ? 'fa-user' : 'fa-robot'} text-sm"></i>
						</div>
						<div class="space-y-1 ${isUser ? 'text-right' : 'text-left'}">
							<div class="inline-block rounded-[1.5rem] px-4 py-3 border ${isUser ? 'border-[#ff6600]/25 bg-[#ff6600]/15 text-white' : 'border-white/10 bg-white/5 text-gray-200'}">
								<p class="whitespace-pre-line leading-relaxed text-sm">${escapeHtml(text)}</p>
							</div>
						</div>
					</div>
				`;

					messageList.appendChild(wrapper);
					scrollToBottom();
				};

				form.addEventListener('submit', async (event) => {
					event.preventDefault();

					const question = input.value.trim();
					if (!question) {
						return;
					}

					const formData = new FormData(form);
					formData.set('message_text', question);

					appendMessage(question, true);
					input.value = '';
					input.focus();

					sendBtn.disabled = true;
					sendBtn.classList.add('opacity-70', 'cursor-not-allowed');

					try {
						const response = await fetch(form.action, {
							method: 'POST',
							body: formData,
							headers: {
								'X-Requested-With': 'XMLHttpRequest',
								'Accept': 'application/json'
							}
						});

						const data = await response.json();

						if (!response.ok || !data.success) {
							appendMessage(data.message || 'Không thể gửi câu hỏi lúc này.', false);
							return;
						}

						appendMessage(data.reply, false);
					} catch (error) {
						appendMessage('Không thể kết nối tới máy chủ. Vui lòng thử lại.', false);
					} finally {
						sendBtn.disabled = false;
						sendBtn.classList.remove('opacity-70', 'cursor-not-allowed');
					}
				});

				scrollToBottom();
			})();
		</script>
	</div>
</div>