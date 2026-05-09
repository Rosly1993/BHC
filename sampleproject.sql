/*
 Navicat Premium Dump SQL

 Source Server         : 10.216.15.10
 Source Server Type    : MySQL
 Source Server Version : 120101 (12.1.1-MariaDB)
 Source Host           : 10.216.15.10:3306
 Source Schema         : sampleproject

 Target Server Type    : MySQL
 Target Server Version : 120101 (12.1.1-MariaDB)
 File Encoding         : 65001

 Date: 09/05/2026 10:16:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_dosage
-- ----------------------------
DROP TABLE IF EXISTS `tbl_dosage`;
CREATE TABLE `tbl_dosage`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Dosage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Isactive` int NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_dosage
-- ----------------------------
INSERT INTO `tbl_dosage` VALUES (1, 'N/A', 1);
INSERT INTO `tbl_dosage` VALUES (2, '5mg', 1);
INSERT INTO `tbl_dosage` VALUES (3, '10mg', 1);
INSERT INTO `tbl_dosage` VALUES (4, '20mg', 1);
INSERT INTO `tbl_dosage` VALUES (5, '40mg', 1);
INSERT INTO `tbl_dosage` VALUES (6, '50mg', 1);
INSERT INTO `tbl_dosage` VALUES (7, '100mg', 1);
INSERT INTO `tbl_dosage` VALUES (8, '250mg', 1);
INSERT INTO `tbl_dosage` VALUES (9, '500mg', 1);

-- ----------------------------
-- Table structure for tbl_issuance
-- ----------------------------
DROP TABLE IF EXISTS `tbl_issuance`;
CREATE TABLE `tbl_issuance`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Med_id` int NULL DEFAULT NULL,
  `IssuedTo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Age` int NULL DEFAULT NULL,
  `Qty` int NULL DEFAULT NULL,
  `DateIssued` datetime NULL DEFAULT NULL,
  `IssuedBy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_issuance
-- ----------------------------
INSERT INTO `tbl_issuance` VALUES (1, 2, 'Rosly Rapada', 18, 200, '2026-05-03 09:53:08', '1');
INSERT INTO `tbl_issuance` VALUES (2, 3, 'Rosly Rapada', 18, 10, '2026-05-05 09:59:51', '1');
INSERT INTO `tbl_issuance` VALUES (3, 1, 'Rosly Rapada', 18, 20, '2026-05-06 11:47:20', '1');
INSERT INTO `tbl_issuance` VALUES (4, 2, 'Jay Yumul', 60, 30, '2026-05-06 14:01:15', '1');

-- ----------------------------
-- Table structure for tbl_med_history
-- ----------------------------
DROP TABLE IF EXISTS `tbl_med_history`;
CREATE TABLE `tbl_med_history`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Med_id` int NULL DEFAULT NULL,
  `Action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Qty_Change` int NULL DEFAULT NULL,
  `Created_At` timestamp NULL DEFAULT NULL,
  `User_Id` int NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_med_history
-- ----------------------------
INSERT INTO `tbl_med_history` VALUES (1, 1, 'STOCK_IN', 'Updated medication details. Stock increased by 4.', 4, '2026-05-07 00:43:15', 1);
INSERT INTO `tbl_med_history` VALUES (2, 3, 'CREATED', 'Registered new medication. Initial stock set to 10.', 10, '2026-05-07 00:44:00', 1);
INSERT INTO `tbl_med_history` VALUES (3, 3, 'STOCK_IN', 'Updated medication details. Stock increased by 6.', 6, '2026-05-07 01:27:36', 1);
INSERT INTO `tbl_med_history` VALUES (4, 3, 'STOCK_IN', 'Updated medication details. Stock increased by 9.', 9, '2026-05-07 01:29:23', 1);
INSERT INTO `tbl_med_history` VALUES (5, 2, 'STOCK_IN', 'Updated medication details. Stock increased by 90.', 90, '2026-05-07 09:30:02', 1);
INSERT INTO `tbl_med_history` VALUES (6, 2, 'ISSUED', 'Issued 200 units to Rosly Rapada', -200, '2026-05-07 09:53:08', 1);
INSERT INTO `tbl_med_history` VALUES (9, 1, 'ISSUED', 'Issued 1 units to Rosly Rapada', -1, '2026-05-07 09:59:51', 1);
INSERT INTO `tbl_med_history` VALUES (10, 1, 'STOCK_IN', 'Updated medication details. Stock increased by 500.', 500, '2026-05-07 11:46:38', 1);
INSERT INTO `tbl_med_history` VALUES (11, 3, 'ISSUED', 'Issued 20 units to Rosly Rapada', -20, '2026-05-07 11:47:20', 1);
INSERT INTO `tbl_med_history` VALUES (12, 3, 'STOCK_IN', 'Updated medication details. Stock increased by 10.', 10, '2026-05-07 14:00:37', 1);
INSERT INTO `tbl_med_history` VALUES (13, 2, 'ISSUED', 'Issued 30 units to Jay Yumul', -30, '2026-05-07 14:01:15', 1);
INSERT INTO `tbl_med_history` VALUES (14, 4, 'CREATED', 'Registered new medication. Initial stock set to 10.', 10, '2026-05-07 15:36:24', 1);
INSERT INTO `tbl_med_history` VALUES (15, 5, 'CREATED', 'Registered new medication. Initial stock set to 20.', 20, '2026-05-07 15:45:00', 1);
INSERT INTO `tbl_med_history` VALUES (16, 6, 'CREATED', 'Registered new medication. Initial stock set to 20.', 20, '2026-05-07 15:45:57', 1);
INSERT INTO `tbl_med_history` VALUES (17, 5, 'UPDATED', 'Updated medication details.', 0, '2026-05-07 15:50:37', 1);
INSERT INTO `tbl_med_history` VALUES (18, 7, 'CREATED', 'Registered new medication. Initial stock set to 20.', 20, '2026-05-07 15:50:58', 1);
INSERT INTO `tbl_med_history` VALUES (19, 8, 'CREATED', 'Registered new medication. Initial stock set to 20.', 20, '2026-05-07 15:51:18', 1);
INSERT INTO `tbl_med_history` VALUES (20, 9, 'CREATED', 'Registered new medication. Initial stock set to 10.', 10, '2026-05-07 15:52:16', 1);
INSERT INTO `tbl_med_history` VALUES (21, 9, 'UPDATED', 'Updated medication details.', 0, '2026-05-09 07:02:05', 1);
INSERT INTO `tbl_med_history` VALUES (22, 2, 'PURCHASE', 'Added 5 units via Purchase Ref: 111', 5, '2026-05-09 07:07:57', 1);
INSERT INTO `tbl_med_history` VALUES (23, 5, 'PURCHASE', 'Added 5 units via Purchase Ref: 111', 5, '2026-05-09 07:07:58', 1);
INSERT INTO `tbl_med_history` VALUES (24, 9, 'PURCHASE', 'Added 5 units via Purchase Ref: 111', 5, '2026-05-09 07:07:58', 1);
INSERT INTO `tbl_med_history` VALUES (25, 3, 'PURCHASE', 'Added 5 units via Purchase Ref: 111', 5, '2026-05-09 07:07:58', 1);
INSERT INTO `tbl_med_history` VALUES (26, 3, 'PURCHASE', 'Added 5 units via Purchase Ref: 111', 5, '2026-05-09 07:07:58', 1);
INSERT INTO `tbl_med_history` VALUES (27, 1, 'PURCHASE', 'Added 5 units via Purchase Ref: 123', 5, '2026-05-09 07:20:08', 1);
INSERT INTO `tbl_med_history` VALUES (28, 1, 'PURCHASE', 'Added 1111 units via Purchase Ref: fef', 1111, '2026-05-09 07:31:04', 1);
INSERT INTO `tbl_med_history` VALUES (29, 1, 'PURCHASE', 'Added 8 units via Purchase Ref: 123', 8, '2026-05-09 07:37:36', 1);

-- ----------------------------
-- Table structure for tbl_med_inventory
-- ----------------------------
DROP TABLE IF EXISTS `tbl_med_inventory`;
CREATE TABLE `tbl_med_inventory`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Med_id` int NULL DEFAULT NULL,
  `Qty` int NULL DEFAULT NULL,
  `DateAdded` datetime NULL DEFAULT NULL,
  `AddedBy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_med_inventory
-- ----------------------------
INSERT INTO `tbl_med_inventory` VALUES (1, 2, 55, '2026-05-07 09:30:02', '1');
INSERT INTO `tbl_med_inventory` VALUES (2, 1, 1631, '2026-05-09 07:37:36', '1');
INSERT INTO `tbl_med_inventory` VALUES (3, 3, 25, '2026-05-07 14:00:36', '1');
INSERT INTO `tbl_med_inventory` VALUES (4, 4, 10, '2026-05-07 15:36:24', '1');
INSERT INTO `tbl_med_inventory` VALUES (5, 5, 25, '2026-05-07 15:45:00', '1');
INSERT INTO `tbl_med_inventory` VALUES (6, 6, 20, '2026-05-07 15:45:57', '1');
INSERT INTO `tbl_med_inventory` VALUES (7, 7, 20, '2026-05-07 15:50:58', '1');
INSERT INTO `tbl_med_inventory` VALUES (8, 8, 20, '2026-05-07 15:51:18', '1');
INSERT INTO `tbl_med_inventory` VALUES (9, 9, 15, '2026-05-07 15:52:16', '1');

-- ----------------------------
-- Table structure for tbl_med_list
-- ----------------------------
DROP TABLE IF EXISTS `tbl_med_list`;
CREATE TABLE `tbl_med_list`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Dosage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Isactive` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `AddedBy` int NULL DEFAULT NULL,
  `DateAdded` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_med_list
-- ----------------------------
INSERT INTO `tbl_med_list` VALUES (1, 'Losartan', '100mg', 'Maintenance', '1', 1, '2026-05-06 11:54:06');
INSERT INTO `tbl_med_list` VALUES (2, 'Losartan', '50mg', 'Maintenance', '1', 1, '2026-05-06 12:00:51');
INSERT INTO `tbl_med_list` VALUES (3, 'Amlodipine', '5mg', 'Maintenance', '1', 1, '2026-05-07 00:44:00');
INSERT INTO `tbl_med_list` VALUES (4, 'Amlodipine', '10mg', 'Maintenance', '1', 1, '2026-05-07 15:36:23');
INSERT INTO `tbl_med_list` VALUES (5, 'Atorvastatin', '20mg', 'Maintenance', '1', 1, '2026-05-07 15:44:59');
INSERT INTO `tbl_med_list` VALUES (6, 'Telmisartan', '40mg', 'Maintenance', '1', 1, '2026-05-07 15:45:57');
INSERT INTO `tbl_med_list` VALUES (7, 'Allupurinol', '100mg', 'Maintenance', '1', 1, '2026-05-07 15:50:58');
INSERT INTO `tbl_med_list` VALUES (8, 'Febuxostat', '40mg', 'Maintenance', '1', 1, '2026-05-07 15:51:18');
INSERT INTO `tbl_med_list` VALUES (9, 'Ambroxol', 'N/A', 'Medicine', '1', 1, '2026-05-07 15:52:16');

-- ----------------------------
-- Table structure for tbl_purchase_items
-- ----------------------------
DROP TABLE IF EXISTS `tbl_purchase_items`;
CREATE TABLE `tbl_purchase_items`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `PurchaseId` int NULL DEFAULT NULL,
  `Med_id` int NULL DEFAULT NULL,
  `Qty` int NULL DEFAULT NULL,
  `UnitPrice` decimal(10, 2) NULL DEFAULT NULL,
  `Subtotal` decimal(10, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_purchase_items
-- ----------------------------
INSERT INTO `tbl_purchase_items` VALUES (1, 1, 2, 5, 100.00, 500.00);
INSERT INTO `tbl_purchase_items` VALUES (2, 1, 5, 5, 11.00, 55.00);
INSERT INTO `tbl_purchase_items` VALUES (3, 1, 9, 5, 11.00, 55.00);
INSERT INTO `tbl_purchase_items` VALUES (4, 1, 3, 5, 11.00, 55.00);
INSERT INTO `tbl_purchase_items` VALUES (5, 1, 3, 5, 11.00, 55.00);
INSERT INTO `tbl_purchase_items` VALUES (6, 2, 1, 5, 9.00, 45.00);
INSERT INTO `tbl_purchase_items` VALUES (7, 3, 1, 1111, 11.00, 12221.00);
INSERT INTO `tbl_purchase_items` VALUES (8, 4, 1, 8, 9.00, 72.00);

-- ----------------------------
-- Table structure for tbl_purchases
-- ----------------------------
DROP TABLE IF EXISTS `tbl_purchases`;
CREATE TABLE `tbl_purchases`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `ReferenceNo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `PurchaseDate` date NULL DEFAULT NULL,
  `TotalAmount` decimal(10, 2) NULL DEFAULT NULL,
  `AttachmentPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `CreatedBy` int NULL DEFAULT NULL,
  `CreatedAt` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_purchases
-- ----------------------------
INSERT INTO `tbl_purchases` VALUES (1, '111', '2026-05-09', NULL, '1778281677_30dea100de9e087b61e0.pdf', 1, '2026-05-09 07:07:57');
INSERT INTO `tbl_purchases` VALUES (2, '123', '2026-05-09', NULL, '1778282408_3657a956d0a350210de8.pdf', 1, '2026-05-09 07:20:08');
INSERT INTO `tbl_purchases` VALUES (3, 'fef', '2026-05-09', NULL, '1778283063_b7a88eb7100300c88d39.pdf', 1, '2026-05-09 07:31:03');
INSERT INTO `tbl_purchases` VALUES (4, '123', '2026-05-09', NULL, '1778283456_5a986493317c06f71a0f.pdf', 1, '2026-05-09 07:37:36');

-- ----------------------------
-- Table structure for tbl_role
-- ----------------------------
DROP TABLE IF EXISTS `tbl_role`;
CREATE TABLE `tbl_role`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Isactive` int NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_role
-- ----------------------------
INSERT INTO `tbl_role` VALUES (1, 'Admin', 1);
INSERT INTO `tbl_role` VALUES (2, 'Staff', 1);
INSERT INTO `tbl_role` VALUES (3, 'Guest', 1);

-- ----------------------------
-- Table structure for tbl_type
-- ----------------------------
DROP TABLE IF EXISTS `tbl_type`;
CREATE TABLE `tbl_type`  (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `Isactive` int NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_type
-- ----------------------------
INSERT INTO `tbl_type` VALUES (1, 'Maintenance', 1);
INSERT INTO `tbl_type` VALUES (2, 'Vitamins', 1);
INSERT INTO `tbl_type` VALUES (3, 'Medicine', 1);
INSERT INTO `tbl_type` VALUES (4, 'Antibiotics', 1);

-- ----------------------------
-- Table structure for tbl_user
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `middlename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `emailaddress` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `contactnumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci NULL DEFAULT NULL,
  `role` int NULL DEFAULT NULL,
  `isactive` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_uca1400_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_user
-- ----------------------------
INSERT INTO `tbl_user` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Rosly', 'Barlongay', 'Rapada', 'rosly.rapada@nidec.com', '118', 1, 1);
INSERT INTO `tbl_user` VALUES (2, 'Guest', 'adb831a7fdd83dd1e2a309ce7591dff8', 'Guest', 'Guest', 'Guest', 'guest@mail.com', '123456', 3, 1);

SET FOREIGN_KEY_CHECKS = 1;
