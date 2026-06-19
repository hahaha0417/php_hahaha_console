# Workflow Notes

## Read

- 先鎖定 provider，再用對應 service 讀最小範圍資料。
- dashboard 類需求優先用 service 內建的 `*_Dashboard_Get` 方法。

## Write

- 先確認 target id、repo、board、issue key、message id、group id。
- 卡片、issue、message、draft、圖片附件與 rich menu 都走 service 方法，不在 controller 直接拼 `Http::`。

## Image

- Trello、Jira 走 multipart attachment。
- GitHub 圖片檔走 contents API，內容用 base64。
- Outlook 圖片附件走 Graph fileAttachment。
- Gmail 圖片寄信走 raw MIME。
- LINE 圖片訊息走 URL 型 message，Flex card 走 JSON contents。
