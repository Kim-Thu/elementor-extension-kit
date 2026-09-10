# Elementor Extension Kit

Bộ khung addon Elementor theo hướng module tái sử dụng, hiệu năng tốt và dễ mang từng element/block sang nhiều dự án.

## Nguyên tắc chính

- PSR-4 namespace: `ElementorExtensionKit\\`
- Prefix công khai: `eek-`
- Mỗi element/block tự chứa PHP, CSS, JS và metadata của chính nó.
- Xóa một element/block chỉ cần xóa đúng thư mục của element/block đó và bỏ đăng ký tương ứng.
- Không gom CSS/JS của nhiều element vào một thư mục asset dùng chung nếu asset đó không thực sự dùng chung.
- Shared asset chỉ dành cho foundation/runtime thực sự dùng toàn plugin.

## Cấu trúc

```text
src/
├── Core/
├── Contracts/
├── Shared/
└── Elements/
    └── Content/
        └── Card/
            ├── CardWidget.php
            ├── element.json
            ├── css/
            │   └── card.css
            └── js/
                └── card.js
```

Xem thêm `docs/NAMING.md` và `docs/ARCHITECTURE.md`.
