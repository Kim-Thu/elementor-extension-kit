# Element Contract

## Purpose

Contract này giữ registry nhất quán nhưng không ép element vào kiến trúc nhiều lớp. Phase 1 dùng manual review; không yêu cầu validator/CI riêng.

## Minimum manifest

Mỗi element có `element.json` với 4 field bắt buộc:

```json
{
  "id": "card",
  "name": "Card",
  "class": "ElementorExtensionKit\\Elements\\Content\\Card\\Card",
  "handle": "eek-card"
}
```

Optional assets chỉ khai báo khi file thực sự tồn tại:

```json
{
  "id": "card",
  "name": "Card",
  "class": "ElementorExtensionKit\\Elements\\Content\\Card\\Card",
  "handle": "eek-card",
  "style": "css/card.css",
  "script": "js/card.js"
}
```

## Field rules

- `id`: lowercase kebab-case, bắt đầu bằng chữ cái, regex `^[a-z][a-z0-9-]*$`.
- `name`: tên hiển thị có ý nghĩa cho element.
- `class`: class chính của element, mặc định theo `ElementorExtensionKit\\Elements\\{Domain}\\{Element}\\{Element}`.
- `handle`: unique trong catalog và dùng prefix `eek-`.
- `style`: optional relative path bên trong chính module.
- `script`: optional relative path bên trong chính module.

Không yêu cầu suffix `Widget`.

## Templates

`templates/` không phải manifest field bắt buộc. Element tự chọn template local khi có nhiều layout markup khác nhau.

Template path phải đến từ allow-list/layout map do code kiểm soát. Không nối path từ input chưa kiểm soát.

Element chỉ có một markup không cần tạo `templates/`.

## Asset locality and safety

Asset path:

- phải là relative path;
- không được bắt đầu bằng `/` hoặc Windows drive path;
- không được chứa segment `..`;
- file phải tồn tại;
- resolved file phải nằm bên trong module directory;
- missing/invalid optional asset được bỏ qua, không fatal.

Registry cache manifest paths và decoded manifest trong cùng request để tránh scan/read lặp không cần thiết.

## Class safety

Class manifest:

- phải nằm dưới namespace `ElementorExtensionKit\\Elements\\`;
- phải tồn tại;
- phải kế thừa `Elementor\\Widget_Base`;
- nếu class thiếu hoặc sai contract, registry bỏ qua element thay vì fatal.

## Duplicate handle

`handle` phải unique giữa các element. Registry giữ handle đầu tiên trong asset registration và bỏ qua duplicate tiếp theo để tránh asset collision. Duplicate vẫn là lỗi catalog cần sửa thủ công.

## Valid optional cases

Element không cần JavaScript:

```json
{
  "id": "divider-pro",
  "name": "Divider Pro",
  "class": "ElementorExtensionKit\\Elements\\Content\\DividerPro\\DividerPro",
  "handle": "eek-divider-pro",
  "style": "css/divider-pro.css"
}
```

Element không cần CSS/JS:

```json
{
  "id": "semantic-wrapper",
  "name": "Semantic Wrapper",
  "class": "ElementorExtensionKit\\Elements\\Layout\\SemanticWrapper\\SemanticWrapper",
  "handle": "eek-semantic-wrapper"
}
```

Cả hai đều hợp lệ. Class chỉ khai báo `get_style_depends()` / `get_script_depends()` cho asset thực sự có.

## Design-system contract

`docs/DESIGN_SYSTEM.md` là contract cho visual ownership.

- Elementor Site Settings/Kit owns Global Colors, Global Fonts và native site design settings khi applicable.
- EEK không tạo Global Colors/Fonts source of truth riêng.
- Nếu element thực sự có color/typography style control, control đó phải dùng native Elementor global-style contract khi semantics phù hợp.
- Nếu element không cần style control, không tạo control giả chỉ để expose global settings; CSS của element inherit qua `eek-design-system`.
- Reusable spacing/radius/shadow/border/surface/state/component metrics dùng `--eek-*` semantic-gap tokens.
- Functional/local geometry có thể giữ local nếu chưa có reusable semantics thực tế.
- Explicit local Elementor override có precedence cao hơn global/default values.

## Shared rule

Không đưa helper/attribute/component vào `Shared/` trước khi có ít nhất 2 consumer thực tế với cùng semantics và reuse làm code đơn giản hơn. Shared không được làm element kéo theo dependency/runtime cost không cần thiết.

## Manual review checklist

- [ ] 4 required fields có giá trị hợp lệ.
- [ ] Class path đúng namespace và class chính `{Element}`.
- [ ] Handle unique và có prefix `eek-`.
- [ ] Optional asset chỉ khai báo khi cần và file nằm trong module.
- [ ] Không có absolute path, `../` hoặc path traversal.
- [ ] Template optional và layout được map bằng allow-list.
- [ ] Element không có JS/CSS/template vẫn hợp lệ khi field/folder tương ứng được omit.
- [ ] Elementor-owned color/typography dùng Elementor globals khi applicable; không duplicate global settings.
- [ ] Reusable design gaps dùng semantic `--eek-*` tokens thay vì lặp hard-coded constants.
- [ ] Không tạo abstraction/Shared chỉ vì dự đoán reuse tương lai.
