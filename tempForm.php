<form method="post">
<input type="hidden" name="json" value='{"QuestionId":241966,"NameTextId":null,"MaxLength":100,"Size":60,"RegularExpressionGroupId":null,"CustomInputTypeId":null,"AllowMultiple":0,"NameText":{"NameTextId":null,"LanguageId":null,"MediaId":null,"Name":null},"CustomInputType":{"CustomInputTypeId":null,"Type":null},"RegularExpressionGroup":{"RegularExpressionGroupId":null,"Name":null,"Description":null,"DefaultTextId":null,"ErrorTextId":null,"IsNumeric":null,"ToUpperCase":null,"ErrorText":{"NameTextId":null,"LanguageId":null,"MediaId":null,"Name":null},"DefaultText":{"NameTextId":null,"LanguageId":null,"MediaId":null,"Name":null},"RegularExpression":[{"RegularExpressionId":null,"RegularExpressionGroupId":null,"RegularExpression":null,"Description":null}]},"SessionId":1858493,"InstanceNumber":1,"ResponseDate":"2015-01-16 15:12:50","ResponseId":2887771,"EditedBy":null,"IsPrefilled":0,"NeedsReview":0,"IsDeleted":0,"ResponseText":{"Response":"22333e","IsEncrypted":1,"ResponseId":2887771}}'>
<input type="submit">
</form>

<?php
echo implode($_REQUEST,",");
?>