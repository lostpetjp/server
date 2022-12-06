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
| 1 | 移転 (案件) | 
| 2 | 移転 (メディア) | 
| 3 | 移転 (コメント) | 
| 4 | clean up (`media-relation`) | 
| 5 | clean up (`contact`) | 
| 6 | `CaseCount::updateAll()` | 
| 7 | clean up (`case-version`) | 
| 8 | `Queue::batch()` | 
| 9 | case expires | 
| 10 | case (`archive=1`) |
| 11 | clean up (`queue`) | 
| 12 | media (`archive=1`)。 | 
| 13 | media (`archive=2`) |  
| 14 | comment (`archive=1`) | 

## Table
| ID | 説明 |
| -- | -- |
| 1 | 定期実行するスクリプトの管理。 | 
| 2 | 案件の移転状況の管理。 | 
| 3 | コメントの移転状況の管理。 | 
| 4 | メディア (check archive) | 
| 5 | コメント (`archive=1`) | 
