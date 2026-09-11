# Development Workflow

## Purpose

Mọi thay đổi phải truy vết được theo chuỗi: **Issue → Branch → Commit → Verification → Result**.

Phase đang active phải giữ scope đã chốt. Thay đổi kiến trúc mới không được chen vào phase hiện tại; ghi thành change request và đưa sang phase sau trừ khi đó là fix bắt buộc để đạt chính baseline của phase hiện tại.

## Issue contract

Mỗi issue bắt buộc có các phần:

1. `Goal` — vấn đề/mục tiêu duy nhất cần đạt.
2. `Scope` — vùng code được phép thay đổi khi cần giới hạn.
3. `Actions` — các hành động cụ thể để đạt Goal.
4. `Acceptance Criteria` — điều kiện nhị phân để coi là đạt.
5. `Verification` — cách kiểm chứng rule/kết quả.
6. `Expected Result` — trạng thái cuối cùng mong muốn.
7. `Done Checklist` — checklist trước khi đóng issue.

Một issue không nên chứa nhiều mục tiêu độc lập. Nếu có thể đóng một phần mà phần còn lại vẫn có giá trị độc lập, phải tách issue.

## Implementation simplicity rule

Ưu tiên cấu trúc nhỏ nhất giải quyết đúng nhu cầu hiện tại.

Element mặc định:

```text
src/Elements/{Domain}/{Element}/
├── {Element}.php
├── element.json
├── templates/   # optional
├── css/         # optional
└── js/          # optional
```

Rules:

- Element đơn giản dùng một class chính `{Element}.php`.
- Chỉ tách `templates/` khi có nhiều layout/markup thực sự khác nhau hoặc markup đủ lớn để việc tách làm code dễ đọc hơn.
- Không tạo `Widget`, `Logic`, `Service`, `Renderer`, `Repository` hoặc helper riêng nếu chỉ pass-through hoặc chưa có complexity/reuse thực tế.
- CSS/JS chỉ tồn tại khi element thực sự cần asset đó; không tạo file rỗng để đủ cấu trúc.
- Không tạo abstraction vì dự đoán “có thể cần sau này”.

## Shared extraction rule

Chỉ extract sang `Shared/` khi đồng thời:

- có ít nhất 2 consumer thực tế;
- cùng semantics, không chỉ giống tên;
- giảm duplication rõ ràng;
- không tăng coupling đáng kể;
- không tạo thêm runtime lookup/load không cần thiết;
- copy/remove element vẫn rõ dependency cần mang theo/bỏ đi;
- lợi ích reuse lớn hơn chi phí indirection.

Nếu chưa đạt các điều kiện trên, code ở lại module đang sở hữu nó.

## Branch convention

Format bắt buộc:

```text
t{issue}-{type}-{YYYYMMDD-HHMMSS}-{slug}
```

Ví dụ hợp lệ:

```text
t3-refactor-20260911-064345-card-separation
t4-feat-20260911-064345-element-contract
t5-ci-20260911-064345-quality-gates
```

Type cho phép tối thiểu: `feat`, `fix`, `refactor`, `chore`, `docs`, `test`, `ci`, `perf`, `security`.

Regex tham chiếu:

```regex
^t[0-9]+-(feat|fix|refactor|chore|docs|test|ci|perf|security)-[0-9]{8}-[0-9]{6}-[a-z0-9]+(?:-[a-z0-9]+)*$
```

Không hợp lệ:

```text
feature/card
fix-card
t3/card
t3-refactor-card
```

## Commit convention

Commit phải cho biết **khi nào**, **issue nào**, **loại thay đổi**, và **kết quả đã đạt được**.

Format:

```text
[{YYYYMMDD-HHMMSS}] t{issue} {type}: {action/result}

Result: {kết quả kiểm chứng được đã đạt}
Verify: {cách đã kiểm tra hoặc trạng thái kiểm tra}
```

Ví dụ:

```text
[20260911-065506] t3 refactor: point Card manifest to simplified class

Result: element.json registers the single Card class while CSS/JS stay module-local.
Verify: Manifest class path matches the PSR-4 element structure.
```

Commit không được chỉ ghi `update`, `fix`, `changes`, `cleanup` hoặc nội dung không nêu kết quả.

## Labels

Dùng label theo nhóm:

- `type:*` — loại công việc: `feat`, `fix`, `refactor`, `chore`, `ci`, ...
- `scope:*` — vùng ảnh hưởng: `core`, `workflow`, `element`, `manifest`, ...
- `rule:*` — rule phải chứng minh: `security`, `asset-locality`, `traceable`, `reusability`, ...
- `status:*` — trạng thái thực thi: `ready`, `in-progress`, `blocked`, `done`.

## Verification policy for Phase 1

Phase 1 dùng verification thủ công có evidence rõ ràng. CI/CD hoặc automated validator không phải điều kiện đóng issue ở phase này.

Verification có thể gồm:

- PHP syntax check;
- review namespace/class/manifest;
- kiểm tra output escaping;
- kiểm tra asset locality/path safety;
- kiểm tra copy/remove module;
- test case thủ công cho valid/invalid input khi cần.

Automation chỉ được thêm ở phase sau khi chi phí bảo trì hợp lý và nhu cầu đã rõ.

## Definition of Done

Không đóng issue cho đến khi:

- [ ] Tất cả Acceptance Criteria đã đạt.
- [ ] Tất cả Verification có kết quả rõ ràng.
- [ ] Branch đúng convention.
- [ ] Commit truy vết được issue và có timestamp/result.
- [ ] Không phát sinh file ngoài Scope mà không được giải thích.
- [ ] Không phá rule kiến trúc, security hoặc asset locality.
- [ ] Không thêm abstraction/Shared chưa chứng minh được nhu cầu.
- [ ] Expected Result đã thực sự đạt, không chỉ hoàn thành Actions.
- [ ] Nếu phát sinh thay đổi kiến trúc ngoài baseline phase hiện tại, thay đổi đó đã được chuyển sang phase/change request riêng.
