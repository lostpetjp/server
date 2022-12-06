const textareaE = document.getElementById("text");
const listE = document.getElementById("list");
const resultE = document.getElementById("result");
const shareAE = document.getElementById("share");

textareaE.addEventListener("select", (event) => {
  const text = textareaE.value.substring(textareaE.selectionStart, textareaE.selectionEnd);

  if (document.activeElement === textareaE) {
    const itemE = document.createElement("li");
    const labelE = document.createElement("label");
    labelE.style.display = "block";
    labelE.style.cursor = "pointer";
    itemE.appendChild(labelE);
    const inputE = document.createElement("input");
    inputE.type = "checkbox";
    inputE.name = "features";
    inputE.checked = true;
    labelE.append(inputE, new Text(text));
    listE.appendChild(itemE);

    inputE.addEventListener("change", () => {
      updateText();
    });

    updateText();
  }
});

function updateText() {
  let featuresTexts = [];

  Array.from(document.querySelectorAll("input:checked")).forEach((el) => {
    featuresTexts.push("・" + el.nextSibling.data);
  });

  resultE.value = data.prefix + "\n\n" + featuresTexts.join("\n") + data.suffix;

  shareAE.href = "https://twitter.com/share?hashtags=" + encodeURIComponent(data.hashtags) + "&text=" + encodeURIComponent(resultE.value);

};

updateText();