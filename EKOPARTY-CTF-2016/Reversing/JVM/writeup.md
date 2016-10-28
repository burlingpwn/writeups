# JVM - Reversing 25

```
Bytecodes everywhere, reverse them.

Attachment
rev25_3100aa76fca4432f.zip 
```

Running the "binary" isn't very illustrative, but JD-GUI gives us this:

```
public class EKO
{
  public static void main(String[] paramArrayOfString)
  {
    int i = 0;
    for (int j = 0; j < 1337; j++) {
      i += j;
    }
    String str = "EKO{" + i + "}";
  }
}
```

So the flag is the sum 0 + 1 + ... + 1336, which is given by n (n + 1) / 2, where n is 1336 (remember Calc II?). So the flag is `EKO{893116}`. Cheers!
