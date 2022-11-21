# HTML process
HTMLを表示するプロセス。

## カテゴリ

### top
| path | 説明 |
| -- | -- |
| `/` | トップページ。 |

### case
| path | 説明 |
| -- | -- |
| `/{id}` | 各案件。 |

### documents
| path | 説明 |
| -- | -- |
| `/terms` | 利用規約。 |
| `/privacy` | プライバシーポリシー。 |

### search
| path | 説明 |
| -- | -- |
| `/search/{query}` | 検索。 |


## 構成
- HTMLは全てのページで下記の構成を守る。
- SPAで遷移する時は`{コンテンツ}`を更新する。
- 横幅により、`{ヘッダー}`の右側にドロワーボタンを配置する。
- ページ移動する時に、サイドメニューの現在位置をハイライトする。
- ページ移動する時に、`<head>`のオープングラフ関連の要素を更新する。

### 全体
```html
<body>
  <header>{ヘッダー}</header>
  <div>
    <main>{コンテンツ}</main>
    <nav>{サイドメニュー}</nav>
  </div>
  <footer>{フッター}</footer>
</body>
```

### header (`.d1`)
```html
<header class="d1">
  <a class="d1c" role="button"><img src="/drawer.svg"></a>
  <a class="d1a" href="/"><img src="/logo.svg"></a>
  <a class="d1b" href="">登録</a>
  <!-- ユーザーアイコン(未定) -->
</header>
```

```css
.d1 {
  display: flex;
  align-items: center;
  column-gap: 8px;
}

.d1a {
  margin-right: auto;
}
```
