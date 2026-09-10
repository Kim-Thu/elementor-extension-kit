# Development Workflow

## Purpose

Mọi thay đổi phải truy vết được theo chuỗi: **Issue → Branch → Commit → Verification → Result**.

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
[20260911-064600] t3 refactor: separate Card rendering from Elementor orchestration

Result: CardWidget no longer owns primary markup; view data and template are isolated inside the Card module.
Verify: PHP responsibilities reviewed; assets/template remain module-local.
```

Commit không được chỉ ghi `update`, `fix`, `changes`, `cleanup` hoặc nội dung không nêu kết quả.

## Labels

Dùng label theo nhóm:

- `type:*` — loại công việc: `feat`, `fix`, `refactor`, `chore`, `ci`, ...
- `scope:*` — vùng ảnh hưởng: `core`, `workflow`, `element`, `manifest`, ...
- `rule:*` — rule phải chứng minh: `srp`, `security`, `asset-locality`, `traceable`, ...
- `status:*` — trạng thái thực thi: `ready`, `in-progress`, `blocked`, `verified`.

## Definition of Done

Không đóng issue cho đến khi:

- [ ] Tất cả Acceptance Criteria đã đạt.
- [ ] Tất cả Verification có kết quả rõ ràng.
- [ ] Branch đúng convention.
- [ ] Commit truy vết được issue và có timestamp/result.
- [ ] Không phát sinh file ngoài Scope mà không được giải thích.
- [ ] Không phá rule kiến trúc, security hoặc asset locality.
- [ ] Expected Result đã thực sự đạt, không chỉ hoàn thành Actions.
