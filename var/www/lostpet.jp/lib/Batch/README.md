# Batch
- バッチ処理を適切に行なう。

## DB
- `現在 > 更新日時 + 頻度`、かつ、`in_array(現在, 実行できる時間, true)`を満たす場合に実行する。

```typescript
{
  id: number  // ID
  span: number  // 頻度(秒)
  hour: string  // JSON(Array<number>) 実行できる時間
  updated_at: number  // 更新日時
}
```

## Job
- `./entries/{ID}.php`を実行する。
- 実行したら、更新日時を更新する。

| ID | 説明 |
| -- | -- |
| | | 
