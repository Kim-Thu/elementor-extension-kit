# Architecture

## Goal

Mỗi element là một module có thể thêm, bỏ hoặc mang sang dự án khác với số thay đổi tối thiểu.

## Module boundary

Một element gồm:

```text
src/Elements/{Domain}/{Element}/
├── {Element}Widget.php
├── {Element}Logic.php
├── element.json
├── templates/
│   └── {element}.php
├── css/
└── js/
```

`element.json` là manifest duy nhất để registry biết class và asset của element.

## Responsibility split

- `{Element}Widget.php`: metadata Elementor, controls, dependencies và orchestration.
- `{Element}Logic.php`: chuẩn hóa view data / logic presentation, không render HTML.
- `templates/*.php`: chỉ render markup từ view model đã chuẩn hóa.
- `css/`: style chỉ thuộc element đó.
- `js/`: behavior chỉ thuộc element đó.
- `element.json`: metadata/asset manifest của module.

## Rules

1. Element không được phụ thuộc trực tiếp vào element khác.
2. Code dùng chung phải đi qua `Shared` hoặc một abstraction rõ trách nhiệm.
3. Asset riêng luôn nằm cùng module element; không đưa vào một `assets/css/widgets.css` hoặc `assets/js/widgets.js` toàn cục.
4. Asset được `register`, widget tự khai báo dependency để Elementor chỉ enqueue khi cần.
5. Không có side effect ở thời điểm file được autoload ngoài khai báo class.
6. Global symbols phải có prefix/namespace riêng.
7. Input phải sanitize/validate theo ngữ cảnh; output phải escape ở điểm render.
8. Hook/action/filter đăng ký tập trung trong lifecycle, không rải trong constructor widget.
9. Widget không chứa primary markup; template không chứa Elementor control/lifecycle logic.
10. Logic class không phụ thuộc template hoặc echo output.

## Reuse

Để bỏ `Card` khỏi một dự án, xóa thư mục:

```text
src/Elements/Content/Card/
```

Registry quét manifest nên không có danh sách asset tập trung phải dọn thêm.

Để mang `Card` sang dự án khác, copy nguyên thư mục module và giữ namespace/root convention tương ứng.

## Shared asset threshold

Chỉ tạo shared CSS/JS khi:

- có ít nhất hai module thực sự dùng chung;
- hành vi đó là infrastructure/foundation, không phải style riêng;
- xóa một module không làm shared asset mất ý nghĩa.

Ví dụ phù hợp: design tokens, focus-visible helper, motion utility. Không phù hợp: CSS của Card và Pricing gom chung vì "đều là card".
