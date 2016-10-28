# Certified Excel Hacker - Forensics 50

```
Can you wait for the answer?

Hint
Do not wait for it, it is already there :)

Attachment
for50_ed4b8625b6be1bd0.zip
```

It's an Excel file w/ a macro that seems to hang forever. The problem asks, "Can you wait for it?" But a hint was later added saying "Do not wait for it, it is already there :)" However, the VBA project inside the sheet is "locked," so we'll need to find a way in. Obviously, Excel has some way of running the code without the password, so this is some sort of obfuscation at best. I could not possibly explain how to break it any better than [this](https://stackoverflow.com/questions/1026483/is-there-a-way-to-crack-the-password-on-an-excel-vba-project).

Once in, we see the macro is doing a bunch of SHA1 and base 64 sixteen-something million times over:

```
Sub CALCULATE()
Dim z57fbbe9a55b7e76e8772bb12c27d0537, NOT_ANSWER
NOT_ANSWER = "NOTNOTNOTNOTNOTNOTNOTNOTNOTNOTNOTNOT"
For z57fbbe9a55b7e76e8772bb12c27d0537 = 1 To 16777216
answer = bfd9aaddb34d3018d0842fe01cd876ce2(answer)
Next z57fbbe9a55b7e76e8772bb12c27d0537
Hoja1.NOT_ANSWER.Text = "EKO{" + Replace(answer, "=", "") + "}"
End Sub
Public Function bfd9aaddb34d3018d0842fe01cd876ce2(ByVal sTextToHash As String)
Dim b5d3ce0d93bdc39075041314952e56a03 As Object, be07792f9d366fe5e26844e720f7fd830 As Object
Dim TextToHash() As Byte
Set b5d3ce0d93bdc39075041314952e56a03 = CreateObject("System.Text.UTF8Encoding")
Set be07792f9d366fe5e26844e720f7fd830 = CreateObject("System.Security.Cryptography.SHA1CryptoServiceProvider")
TextToHash = b5d3ce0d93bdc39075041314952e56a03.Getbytes_4(sTextToHash)
Dim bytes() As Byte
bytes = be07792f9d366fe5e26844e720f7fd830.ComputeHash_2((TextToHash))
bfd9aaddb34d3018d0842fe01cd876ce2 = bbdc49a038db5a02827fb9a3373d77989(bytes)
Set b5d3ce0d93bdc39075041314952e56a03 = Nothing
Set be07792f9d366fe5e26844e720f7fd830 = Nothing
End Function
Private Function bbdc49a038db5a02827fb9a3373d77989(ByRef arrData() As Byte) As String
Dim bc5197ca332c6a81c0e410b8010ffd7c1
Dim bde1a23ed20269f4573007d67e676a5e1
Set bc5197ca332c6a81c0e410b8010ffd7c1 = CreateObject("MSXML2.DOMDocument")
Set bde1a23ed20269f4573007d67e676a5e1 = bc5197ca332c6a81c0e410b8010ffd7c1.createElement("b64")
bde1a23ed20269f4573007d67e676a5e1.DataType = "bin.base64"
bde1a23ed20269f4573007d67e676a5e1.nodeTypedValue = arrData
bbdc49a038db5a02827fb9a3373d77989 = bde1a23ed20269f4573007d67e676a5e1.Text
Set bde1a23ed20269f4573007d67e676a5e1 = Nothing
Set bc5197ca332c6a81c0e410b8010ffd7c1 = Nothing
End Function
```

Honestly, rather than try and understand this horrible, obfuscated VB and all it's library calls, I just decided to estimate how long it would take the macro to complete. I changed the constant to someting smaller and timed it on my wristwatch. According to math, it would take around an hour too complete.

Yeah, I can wait for that.

`EKO{DCEUslnl7DeiLWSdCLi0l1fxdc8}`

However, this wasn't the right flag. At this point, I moved on to something less time-consuming for 50 points.
