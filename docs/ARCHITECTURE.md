# Architecture

## Goal

Mỗi element là một module nhỏ, độc lập, có thể thêm, bỏ hoặc mang sang dự án khác với số dependency và số thay đổi tối thiểu.

## Element module

Cấu trúc mặc định:

```text
src/Elements/{Domain}/{Element}/
├── {Element}.php
├── element.json
├── templates/   # optional: chỉ khi có layout/markup khác nhau thật sự
├── css/         # optional
└── js/          # optional
```

Không bắt buộc `Widget`, `Logic`, `Service`, `Renderer` hoặc class trung gian. Element nhỏ dùng một class chính `{Element}.php`.

## Responsibilities

- `{Element}.php`: Elementor metadata, controls, dependency declarations và orchestration cần thiết.
- `templates/`: markup cho các layout thực sự khác nhau; element tự chọn template local bằng allow-list rõ ràng.
- `css/`: style riêng của element.
- `js/`: behavior riêng của element, chỉ tồn tại khi có behavior cần JavaScript.
- `element.json`: contract tối thiểu để registry biết element class và optional assets.

## Core rules

1. Element không phụ thuộc trực tiếp vào element khác.
2. Asset riêng nằm trong module element và chỉ register khi file thực sự tồn tại.
3. CSS/JS/template là optional; không tạo file rỗng hoặc behavior giả chỉ để đủ cấu trúc.
4. Không có side effect ở thời điểm autoload ngoài khai báo class.
5. Global symbols phải dùng namespace/prefix riêng.
6. Input phải validate/sanitize theo ngữ cảnh; output escape ở render boundary.
7. Template không chứa Elementor lifecycle/control registration.
8. Layout name phải map qua allow-list; không dùng input trực tiếp để tạo path tùy ý.
9. Manifest/asset path không được đi ra ngoài module (`../`, absolute path, path traversal).
10. Registry fail-safe: manifest/class/asset không hợp lệ thì bỏ qua module/phần asset đó, không làm frontend fatal.

## Shared extraction rule

Chỉ tạo `Shared/` khi đồng thời:

- có ít nhất 2 consumer thực tế;
- cùng semantics, không chỉ giống tên;
- giảm duplication rõ ràng;
- không tăng coupling đáng kể;
- không tạo thêm runtime lookup/load không cần thiết;
- copy/remove element vẫn nhìn ra dependency cần mang theo hoặc bỏ đi;
- lợi ích reuse lớn hơn chi phí indirection.

Không tạo Shared vì dự đoán có thể dùng lại trong tương lai.

## Portability

Để bỏ `Card`, xóa nguyên thư mục:

```text
src/Elements/Content/Card/
```

Registry quét manifest nên không có central element list hoặc global widget asset bundle phải dọn thêm.

Để mang Card sang dự án khác, copy module cùng các Shared dependency đã được chứng minh (nếu có).

Chi tiết manifest contract xem `docs/ELEMENT_CONTRACT.md`.
