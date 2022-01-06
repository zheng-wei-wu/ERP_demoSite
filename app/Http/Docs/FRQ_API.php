<?php
/**
 * @OA\Get(
 *     path="/frq/getList",
 *     tags={"Request for quotation (FRQ)"},
 *     summary="隨機取得詢價單資訊",
 *     description="於最新的 20 筆報價單中回傳隨機 1 筆詢價單資訊",
 *     @OA\Response(
 *       response=200,
 *       description="Success"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/frq/getList/pk",
 *     tags={"Request for quotation (FRQ)"},
 *     summary="TEST",
 *     description="TEST",
 *     @OA\Response(
 *       response=200,
 *       description="Success"
 *     )
 * )
 */

 /**
  * @OA\Get(
  *     path="/frq/getList/test",
  *     tags={"Request for quotation (FRQ)"},
  *     summary="TEST",
  *     description="TEST",
  *     @OA\Response(
  *       response=200,
  *       description="Success"
  *     )
  * )
  */

/**
 * @OA\Get(
 *     path="/frq/order/{InquiryID}",
 *     tags={"Request for quotation (FRQ)"},
 *     summary="傳送報價單資訊至 Message Bus",
 *     description="輸入詢價單號 (InquiryID)，傳送指定報價單資訊至 Message Bus (21120004)",
 *     @OA\Parameter(
 *       name="InquiryID",
 *       required=true,
 *       in="path",
 *     ),
 *     @OA\Response(
 *       response=200,
 *       description="Success"
 *     ),
 *     @OA\Response(
 *       response=404,
 *       description="Error"
 *     )
 * )
 */
