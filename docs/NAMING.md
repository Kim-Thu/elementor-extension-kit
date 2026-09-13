# Naming Convention

Mục tiêu: tên ổn định, dễ tìm, ít conflict, có thể tái sử dụng giữa nhiều dự án.

## PHP

- Root namespace: `ElementorExtensionKit\\`
- Namespace theo domain và element: `ElementorExtensionKit\\Elements\\Content\\Card`
- Widget class: `{ElementName}Widget`, ví dụ `CardWidget`
- Service/registry: tên theo trách nhiệm, ví dụ `ElementRegistry`, `AssetRegistry`
- Không dùng tên cá nhân, tên khách hàng hoặc tên dự án trong class dùng chung.

## Elementor widget name

Dùng kebab-case với prefix `eek-`:

- `eek-card`
- `eek-tabs`
- `eek-accordion`

## CSS

Dùng BEM với block trùng widget handle bỏ prefix Elementor kỹ thuật nếu phù hợp, ưu tiên giữ `eek-` để tránh conflict:

- `.eek-card`
- `.eek-card__title`
- `.eek-card__media`
- `.eek-card--horizontal`

Không đặt selector theo trang/dự án như `.home-card`, `.client-a-card` trong element tái sử dụng.

## JavaScript

- Asset handle: trùng widget handle, ví dụ `eek-card`
- Hook Elementor: `frontend/element_ready/eek-card.default`
- Data attribute runtime: `data-eek-*`

## Files

Mỗi element tự chứa toàn bộ asset riêng:

```text
Elements/Content/Card/
├── CardWidget.php
├── element.json
├── css/card.css
└── js/card.js
```

Nếu element không cần JS, bỏ hẳn `js/` và trường `script` khỏi manifest. Nếu không cần CSS, làm tương tự với `css/`.

## Shared

Chỉ đưa code/asset vào `src/Shared/` khi có ít nhất hai element thật sự dùng chung và phần đó không thuộc trách nhiệm riêng của một element.
