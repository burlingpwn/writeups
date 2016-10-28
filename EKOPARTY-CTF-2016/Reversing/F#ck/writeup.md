# F#ck - Reversing 50

```
The miracle of the expressive functional programming, is it really functional?

Attachment
rev50_3511a8cd66b371eb.zip 
```

The challenged is called F#ck, as in F#, Micro$oft's OCaml-inspired programming language for .NET. After installing `fsharp` on Ubuntu,

```
$ mono FlagGenerator.exe
Usage: FlagGenerator.exe <FLAG>
$ mono FlagGenerator.exe blah
BAD ANSWER
```

I burned some time trying to build the Mono debugger, sdb (the one in the Ubuntu repos was old and didn't seem to support the `args` command), but was having dependency problems that really aren't any fun. Instead, I picked up [ILSpy](http://ilspy.net/), an open source .NET decompiler. Again I had problems running it under Mono, but I just punted on the problem and ran it in a Windows VM instead:

```
// Program
[EntryPoint]
public static int main(string[] argv)
{
	if (argv.Length != 1)
	{
		ExtraTopLevelOperators.PrintFormatLine<Unit>(new PrintfFormat<Unit, TextWriter, Unit, Unit, Unit>("Usage: FlagGenerator.exe <FLAG>"));
	}
	else
	{
		string text = Program.get_flag("t#hs_siht_kc#f");
		if (string.Equals(text, argv[0]))
		{
			FSharpFunc<string, Unit> fSharpFunc = ExtraTopLevelOperators.PrintFormatLine<FSharpFunc<string, Unit>>(new PrintfFormat<FSharpFunc<string, Unit>, TextWriter, Unit, Unit, string>("EKO{%s}"));
			string text2 = text;
			fSharpFunc.Invoke(text2);
		}
		else
		{
			ExtraTopLevelOperators.PrintFormatLine<Unit>(new PrintfFormat<Unit, TextWriter, Unit, Unit, Unit>("BAD ANSWER"));
		}
	}
	return 0;
}
```

There's also a `get_flag` function, but who cares? We can see the string constant `t#hs_siht_kc#f`, which is `f#ck_this_sh#t` backwards.

```
$ mono FlagGenerator.exe 'f#ck_this_sh#t'
EKO{f#ck_this_sh#t}
```
