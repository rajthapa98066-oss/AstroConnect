(function () {
	const EMOJIS = [
		'😀', '😁', '😂', '🤣', '😊', '😍', '😘', '😎', '🤔', '😢',
		'👍', '👎', '👏', '🙏', '🔥', '⭐', '🎉', '💯', '💙', '💜',
	];

	class EmojiButton {
		constructor(options = {}) {
			this.options = options;
			this.handlers = new Map();
			this.picker = document.createElement('div');
			this.picker.className = 'chatify-emoji-picker';
			this.picker.style.cssText = [
				'position:fixed',
				'z-index:99999',
				'display:none',
				'max-width:240px',
				'padding:10px',
				'border:1px solid rgba(0,0,0,0.12)',
				'border-radius:12px',
				'background:#fff',
				'box-shadow:0 12px 30px rgba(0,0,0,0.18)',
				'grid-template-columns:repeat(5, 1fr)',
				'gap:6px',
			].join(';');

			EMOJIS.forEach((emoji) => {
				const button = document.createElement('button');
				button.type = 'button';
				button.textContent = emoji;
				button.style.cssText = [
					'border:0',
					'background:transparent',
					'font-size:20px',
					'line-height:1',
					'padding:6px',
					'cursor:pointer',
					'border-radius:8px',
				].join(';');
				button.addEventListener('click', () => {
					this.emit('emoji', emoji);
					this.hidePicker();
				});
				this.picker.appendChild(button);
			});

			document.addEventListener('click', (event) => {
				if (!this.picker.contains(event.target)) {
					const isButton = event.target && event.target.closest && event.target.closest('.emoji-button');
					if (!isButton) {
						this.hidePicker();
					}
				}
			});

			document.body.appendChild(this.picker);
		}

		on(eventName, handler) {
			const handlers = this.handlers.get(eventName) || [];
			handlers.push(handler);
			this.handlers.set(eventName, handlers);
		}

		emit(eventName, payload) {
			const handlers = this.handlers.get(eventName) || [];
			handlers.forEach((handler) => handler(payload));
		}

		togglePicker(anchor) {
			if (!anchor) {
				return;
			}

			const isVisible = this.picker.style.display === 'grid';
			if (isVisible) {
				this.hidePicker();
				return;
			}

			const rect = anchor.getBoundingClientRect();
			this.picker.style.left = `${Math.min(rect.left, window.innerWidth - 260)}px`;
			this.picker.style.top = `${rect.top - 10}px`;
			this.picker.style.transform = 'translateY(-100%)';
			this.picker.style.display = 'grid';
		}

		hidePicker() {
			this.picker.style.display = 'none';
		}
	}

	window.EmojiButton = EmojiButton;
})();
